<?php

namespace FlyCrud;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Directory
{
    protected $path;
    protected $format;
    protected $filesystem;
    protected $cache = [];

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
     * Getter magic method
     * 
     * @param string $id
     * 
     * @return Document|Directory
     */
    public function __get($id)
    {
        if (isset($this->cache[$id])) {
            return $this->cache[$id];
        }

        if ($this->hasDirectory($id)) {
            return $this->getDirectory($id);
        }

        return $this->getDocument($id);
    }

    /**
     * Setter magic method
     * 
     * @param string             $id
     * @param Document             $document
     */
    public function __set($id, Document $document)
    {
        return $this->saveDocument($id, $document);
    }

    /**
     * Isset magic method
     * 
     * @param string             $id
     */
    public function __isset($id)
    {
        return $this->hasDirectory($id) || $this->hasDocument($id);
    }

    /**
     * Unset magic method
     * 
     * @param string             $id
     */
    public function __unset($id)
    {
        $this->delete($id);
    }

    /**
     * Read and return a document.
     * 
     * @param string $id
     * @param bool   $create
     * 
     * @return Document
     */
    public function getDocument($id, $create = false)
    {
        switch ($this->getCacheType($id)) {
            case 'dir':
                throw new Exception(sprintf('The id "%s" is not a document', $id));

            case 'file':
                return $this->cache[$id];
        }

        $path = $this->getDocumentPath($id);
        $type = $this->getPathType($path);

        if ($type === 'dir') {
            throw new Exception(sprintf('The id "%s" is not a document', $id));
        }

        if ($type === 'file') {
            $source = $this->filesystem->read($path);

            if (is_string($source)) {
                return $this->cache[$id] = new Document($this->format->parse($source), $id);
            }
        } elseif ($create) {
            return $this->cache[$id] = new Document([], $id);
        }

        throw new Exception(sprintf('File "%s" not found', $path));
    }

    /**
     * Read and return a directory.
     * 
     * @param string $id
     * @param bool   $create
     * 
     * @return Document
     */
    public function getDirectory($id, $create = false)
    {
        switch ($this->getCacheType($id)) {
            case 'file':
                throw new Exception(sprintf('The id "%s" is not a directory', $id));

            case 'dir':
                return $this->cache[$id];
        }

        $path = $this->getDirectoryPath($id);
        $type = $this->getPathType($path);
        
        if ($type === 'file') {
            throw new Exception(sprintf('The id "%s" is not a directory', $id));
        }

        if ($type === 'dir' || $create) {
            return $this->cache[$id] = new static($this->filesystem, $path, $this->format);
        }

        throw new Exception(sprintf('Directory "%s" not found', $path));
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
        switch ($this->getCacheType($id)) {
            case 'dir':
                return true;

            case 'file':
                return false;

            default:
                return $this->getPathType($this->getDirectoryPath($id)) === 'dir';
        }
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
        switch ($this->getCacheType($id)) {
            case 'dir':
                return false;

            case 'file':
                return true;

            default:
                return $this->getPathType($this->getDocumentPath($id)) === 'file';
        }
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
        $this->cache[$id] = $document;
        $this->filesystem->put($this->getDocumentPath($id), $this->format->stringify($document->toArray()));

        return $this;
    }

    /**
     * Deletes a document or directory.
     * 
     * @param string $id
     * 
     * @return self
     */
    public function delete($id)
    {
        if ($this->hasDocument($id)) {
            $this->filesystem->delete($this->getDocumentPath($id));
        } elseif ($this->hasDirectory($id)) {
            $this->filesystem->deleteDir($this->getDirectoryPath($id));
        }

        unset($this->cache[$id]);

        return $this;
    }

    /**
     * Returns all documents and directories.
     * 
     * @return array
     */
    public function getAll()
    {
        $all = [];
        $extension = $this->format->getExtension();

        foreach ($this->filesystem->listContents('/'.$this->path) as $info) {
            $id = $info['filename'];

            if (isset($this->cache[$id])) {
                $all[$id] = $this->cache[$id];
                continue;
            }

            if ($info['type'] === 'dir') {
                $all[$id] = $this->cache[$id] = new static($this->filesystem, $info['path'], $this->format);
                continue;
            }

            if ($info['type'] === 'file' && $info['extension'] === $extension) {
                $source = $this->filesystem->read($path);

                if (is_string($source)) {
                    $all[$id] = $this->cache[$id] = new Document($this->format->parse($source), $id);
                    continue;
                }

                throw new Exception(sprintf('Invalid file "%s"', $info['path']));
            }
        }

        return $all;
    }

    /**
     * Returns a file path.
     * 
     * @param string $id
     * 
     * @return string
     */
    protected function getDocumentPath($id)
    {
        return $this->getDirectoryPath($id).'.'.$this->format->getExtension();
    }

    /**
     * Returns a subdirectory path.
     * 
     * @param string $id
     * 
     * @return string
     */
    protected function getDirectoryPath($id)
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

    private function getCacheType($id)
    {
        if (isset($this->cache[$id])) {
            if ($this->cache[$id] instanceof self) {
                return 'dir';
            }

            return 'file';
        }
    }
}
