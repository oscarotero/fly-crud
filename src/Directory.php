<?php
declare(strict_types = 1);

namespace FlyCrud;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use ArrayAccess;
use RuntimeException;

class Directory implements ArrayAccess
{
    protected $path;
    protected $format;
    protected $filesystem;
    protected $documents = [];
    protected $directories = [];

    /**
     * Creates a new directory instance.
     */
    public static function make(string $path, FormatInterface $format): Directory
    {
        return new static(new Filesystem(new Local($path)), '', $format);
    }

    public function __construct(FilesystemInterface $filesystem, string $path, FormatInterface $format)
    {
        $this->filesystem = $filesystem;
        $this->format = $format;
        $this->path = $path;
    }

    /**
     * Read and return a document.
     */
    public function getDocument(string $id): Document
    {
        if (isset($this->documents[$id])) {
            return $this->documents[$id];
        }

        if ($this->hasDocument($id)) {
            $path = $this->getDocumentPath($id);
            $source = $this->filesystem->read($path);

            if (is_string($source)) {
                return $this->documents[$id] = new Document($this->format->parse($source));
            }

            throw new RuntimeException(sprintf('Format error in the file "%s"', $path));
        }

        throw new RuntimeException(sprintf('File "%s" not found', $id));
    }

    /**
     * Read and return a directory.
     */
    public function getDirectory(string $id): Directory
    {
        if (isset($this->directories[$id])) {
            return $this->directories[$id];
        }

        if ($this->hasDirectory($id)) {
            return $this->directories[$id] = new static($this->filesystem, $this->getDirectoryPath($id), $this->format);
        }

        throw new RuntimeException(sprintf('Directory "%s" not found', $id));
    }

    /**
     * Check whether a document exists.
     */
    public function hasDocument(string $id): bool
    {
        if (isset($this->documents[$id])) {
            return true;
        }

        $path = $this->getDocumentPath($id);

        if ($this->filesystem->has($path)) {
            $info = $this->filesystem->getMetadata($path);

            return $info['type'] === 'file';
        }

        return false;
    }

    /**
     * Check whether a document or directory exists.
     */
    public function hasDirectory(string $id): bool
    {
        if (isset($this->directories[$id])) {
            return true;
        }

        $path = $this->getDirectoryPath($id);

        if ($this->filesystem->has($path)) {
            $info = $this->filesystem->getMetadata($path);

            return $info['type'] === 'dir';
        }

        return false;
    }

    /**
     * Saves a document.
     */
    public function saveDocument(string $id, Document $document): self
    {
        $this->documents[$id] = $document;
        $this->filesystem->put($this->getDocumentPath($id), $this->format->stringify($document->getArrayCopy()));

        return $this;
    }

    /**
     * Creates a new directory.
     */
    public function createDirectory(string $id): Directory
    {
        $path = $this->getDirectoryPath($id);
        $this->filesystem->createDir($path);

        return $this->directories[$id] = new static($this->filesystem, $path, $this->format);
    }

    /**
     * Deletes a document.
     */
    public function deleteDocument(string $id): self
    {
        $this->filesystem->delete($this->getDocumentPath($id));
        unset($this->documents[$id]);

        return $this;
    }

    /**
     * Deletes a directory.
     */
    public function deleteDirectory(string $id): self
    {
        $this->filesystem->deleteDir($this->getDirectoryPath($id));
        unset($this->directories[$id]);

        return $this;
    }

    /**
     * Returns all documents.
     */
    public function getAllDocuments(): array
    {
        $documents = [];

        foreach ($this->filesystem->listContents('/'.$this->path) as $info) {
            $id = $info['filename'];

            if ($this->hasDocument($id)) {
                $documents[$id] = $this->getDocument($id);
            }
        }

        return $documents;
    }

    /**
     * Returns all directories.
     */
    public function getAllDirectories(): array
    {
        $directories = [];

        foreach ($this->filesystem->listContents('/'.$this->path) as $info) {
            $id = $info['filename'];

            if ($this->hasDirectory($id)) {
                $directories[$id] = $this->getDirectory($id);
            }
        }

        return $directories;
    }

    /**
     * Returns a file path.
     */
    private function getDocumentPath(string $id): string
    {
        return $this->getDirectoryPath($id).'.'.$this->format->getExtension();
    }

    /**
     * Returns a directory path.
     */
    private function getDirectoryPath(string $id): string
    {
        if ($this->path === '') {
            return "/{$id}";
        }

        return "/{$this->path}/{$id}";
    }

    /**
     * ArrayAccess used to documents.
     *
     * @param string $id
     *
     * @return bool
     */
    public function offsetExists($id)
    {
        return $this->hasDocument($id);
    }

    /**
     * ArrayAccess used to documents.
     *
     * @param string $id
     *
     * @return Document
     */
    public function offsetGet($id)
    {
        return $this->getDocument($id);
    }

    /**
     * ArrayAccess used to documents.
     *
     * @param string   $id
     * @param Document $document
     */
    public function offsetSet($id, $document)
    {
        $this->saveDocument($id, $document);
    }

    /**
     * ArrayAccess used to documents.
     *
     * @param string $id
     */
    public function offsetUnset($id)
    {
        $this->deleteDocument($id);
    }

    /**
     * Property magic method used to directories.
     */
    public function __get(string $id): Directory
    {
        return $this->getDirectory($id);
    }

    /**
     * Property magic method used to directories.
     */
    public function __isset(string $id): bool
    {
        return $this->hasDirectory($id);
    }

    /**
     * Property magic method used to directories.
     */
    public function __unset(string $id)
    {
        $this->deleteDirectory($id);
    }
}
