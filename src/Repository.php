<?php

namespace FlyCrud;

use League\Flysystem\FilesystemInterface;
use ArrayAccess;

abstract class Repository implements ArrayAccess
{
    protected $path;
    protected $extension = '';
    protected $filesystem;
    protected $cache = [];

    /**
     * Init the repository.
     * 
     * @param FilesystemInterface $filesystem
     * @param string              $path
     */
    public function __construct(FilesystemInterface $filesystem, $path = '/')
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
    }

    /**
     * Check if a document exists.
     *
     * @see ArrayAccess
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Returns a row with a specific id.
     *
     * @see ArrayAccess
     *
     * @return Document
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Store a row with a specific id.
     *
     * @see ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        $document = $this->getOrCreate($offset);
        $document->setData($value);
        $this->save($document);
    }

    /**
     * Remove a row with a specific id.
     *
     * @see ArrayAccess
     */
    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            $this->delete($this->get($offset));
        }
    }

    /**
     * Returns all documents.
     * 
     * @return array
     */
    public function getAll()
    {
        $documents = [];

        foreach ($this->filesystem->listContents($this->path) as $info) {
            $id = $info['filename'];
            $documents[$id] = $this->get($id);
        }

        return $documents;
    }

    /**
     * Read and return a document.
     * 
     * @param string $id
     * 
     * @return Document
     */
    public function getOrCreate($id)
    {
        if ($this->has($id)) {
            return $this->get($id);
        }

        return new Document([], $id);
    }

    /**
     * Read and return a document.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function has($id)
    {
        if (isset($this->cache[$id])) {
            return true;
        }

        $path = $this->getPath($id);

        return $this->filesystem->has($path);
    }

    /**
     * Read and return a document.
     * 
     * @param string $id
     * 
     * @return Document
     */
    public function get($id)
    {
        if (isset($this->cache[$id])) {
            return $this->cache[$id];
        }

        $path = $this->getPath($id);
        $source = $this->filesystem->read($path);

        if ($source === false) {
            throw new Exception(sprintf('File not found: %s', $path));
        }

        return $this->cache[$id] = new Document($this->parse($source), $id);
    }

    /**
     * Saves a document.
     * 
     * @param Document $document
     * 
     * @return self
     */
    public function save(Document $document)
    {
        $id = $document->getId();

        $this->cache[$id] = $document;
        $path = $this->getPath($id);
        $source = $this->stringify($document->getData());

        $this->filesystem->put($path, $source);

        return $this;
    }

    /**
     * Deletes a document.
     * 
     * @param Document $document
     * 
     * @return self
     */
    public function delete(Document $document)
    {
        $id = $document->getId();

        $path = $this->getPath($id);
        $this->filesystem->delete($path);
        unset($this->cache[$id]);

        return $this;
    }

    /**
     * Returns the file path of an Id.
     * 
     * @param string $id
     * 
     * @return string
     */
    protected function getPath($id)
    {
        return "{$this->path}/{$id}.{$this->extension}";
    }

    /**
     * Transform the data to a string.
     * 
     * @param array $data
     * 
     * @return string
     */
    abstract protected function stringify(array $data);

    /**
     * Transform the string to an array.
     * 
     * @param string $source
     * 
     * @return array
     */
    abstract protected function parse($source);
}
