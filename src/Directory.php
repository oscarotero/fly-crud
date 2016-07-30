<?php

namespace FlyCrud;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use ArrayAccess;

class Directory implements ArrayAccess
{
    protected $path;
    protected $format;
    protected $filesystem;
    protected $documents = [];
    protected $directories = [];

    /**
     * Creates a new directory instance
     * 
     * @param string $path
     * @param FormatInterface $format
     * 
     * @return static
     */
    public static function make($path, FormatInterface $format)
    {
        return new static(new Filesystem(new Local($path)), '', $format);
    }

    /**
     * Init the directory.
     * 
     * @param FilesystemInterface $filesystem
     * @param string              $path
     * @param FormatInterface     $format
     */
    public function __construct(FilesystemInterface $filesystem, $path, FormatInterface $format)
    {
        $this->filesystem = $filesystem;
        $this->format = $format;
        $this->path = $path;
    }

    /**
     * Read and return a document.
     * 
     * @param string $id
     * 
     * @return Document
     */
    public function getDocument($id)
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

            throw new Exception(sprintf('Format error in the file "%s"', $path));
        }

        throw new Exception(sprintf('File "%s" not found', $path));
    }

    /**
     * Read and return a directory.
     * 
     * @param string $id
     * 
     * @return Document
     */
    public function getDirectory($id)
    {
        if (isset($this->directories[$id])) {
            return $this->directories[$id];
        }

        if ($this->hasDirectory($id)) {
            return $this->directories[$id] = new static($this->filesystem, $this->getDirectoryPath($id), $this->format);
        }

        throw new Exception(sprintf('Directory "%s" not found', $path));
    }

    /**
     * Check whether a document exists.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function hasDocument($id)
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
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function hasDirectory($id)
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
     * 
     * @param string $id
     * @param Document $document
     * 
     * @return self
     */
    public function saveDocument($id, Document $document)
    {
        $this->documents[$id] = $document;
        $this->filesystem->put($this->getDocumentPath($id), $this->format->stringify($document->getArrayCopy()));

        return $this;
    }

    /**
     * Creates a new directory.
     * 
     * @param string $id
     * 
     * @return self
     */
    public function createDirectory($id)
    {
        $path = $this->getDirectoryPath($id);
        $this->filesystem->createDir($path);

        return $this->directories[$id] = new static($this->filesystem, $path, $this->format);
    }

    /**
     * Deletes a document.
     * 
     * @param string $id
     * 
     * @return self
     */
    public function deleteDocument($id)
    {
        $this->filesystem->delete($this->getDocumentPath($id));
        unset($this->documents[$id]);

        return $this;
    }

    /**
     * Deletes a directory.
     * 
     * @param string $id
     * 
     * @return self
     */
    public function deleteDirectory($id)
    {
        $this->filesystem->deleteDir($this->getDirectoryPath($id));
        unset($this->directories[$id]);

        return $this;
    }

    /**
     * Returns all documents.
     * 
     * @return array
     */
    public function getAllDocuments()
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
     * 
     * @return array
     */
    public function getAllDirectories()
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
     * 
     * @param string $id
     * 
     * @return string
     */
    private function getDocumentPath($id)
    {
        return $this->getDirectoryPath($id).'.'.$this->format->getExtension();
    }

    /**
     * Returns a directory path.
     * 
     * @param string $id
     * 
     * @return string
     */
    private function getDirectoryPath($id)
    {
        if ($this->path === '') {
            return "/{$id}";
        }

        return "/{$this->path}/{$id}";
    }

    private function getPathType($path)
    {
        if ($this->filesystem->has($path)) {
            $info = $this->filesystem->getMetadata($path);

            return $info['type'];
        }
    }

    /**
     * ArrayAccess used to documents
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
     * ArrayAccess used to documents
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
     * ArrayAccess used to documents
     * 
     * @param string $id
     * @param Document $document
     */
    public function offsetSet($id, $document)
    {
        $this->saveDocument($id, $document);
    }
    
    /**
     * ArrayAccess used to documents
     * 
     * @param string $id
     */
    public function offsetUnset($id)
    {
        $this->deleteDocument($id);
    }

    /**
     * Property magic method used to directories
     * 
     * @param string $offset
     * 
     * @return Directory
     */
    public function __get($id)
    {
        return $this->getDirectory($id);
    }

    /**
     * Property magic method used to directories
     * 
     * @param string             $id
     */
    public function __isset($id)
    {
        return $this->hasDirectory($id);
    }

    /**
     * Property magic method used to directories
     * 
     * @param string             $id
     */
    public function __unset($id)
    {
        $this->deleteDirectory($id);
    }
}
