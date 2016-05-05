<?php

namespace FlyCrud;

use League\Flysystem\FilesystemInterface;

class Container
{
    protected $repositories = [];

    /**
     * Create a container with various repositories.
     * 
     * @param string $name
     * @param array  $args
     */
    public static function __callStatic($name, $args)
    {
        $class = __NAMESPACE__.'\\'.ucfirst($name);

        if (!class_exists($class)) {
            throw new BadMethodCallException(sprintf('The repository "%s" does not exists', $class));
        }

        return self::create(array_shift($args), $class);
    }

    /**
     * Creates a container with a repository for each subdirectory.
     * 
     * @param FilesystemInterface $filesystem
     * @param string              $class
     * 
     * @return static
     */
    private static function create(FilesystemInterface $filesystem, $class)
    {
        $container = new static();

        foreach ($filesystem->listContents() as $info) {
            if ($info['type'] === 'dir') {
                $container->{$info['filename']} = new $class($filesystem, $info['filename']);
            }
        }

        return $container;
    }

    /**
     * Returns a repository.
     * 
     * @param string $name
     * 
     * @return Repository
     */
    public function __get($name)
    {
        if (!isset($this->repositories[$name])) {
            throw new \InvalidArgumentException(sprintf('The repository %s does not exist', $name));
        }

        return $this->repositories[$name];
    }

    /**
     * Adds a new repository to the container.
     *
     * @param string     $name
     * @param Repository $repository
     */
    public function __set($name, Repository $repository)
    {
        $this->repositories[$name] = $repository;
    }
}
