<?php

namespace FlyCrud;

use League\Flysystem\FilesystemInterface;

class Container
{
    protected $repositories = [];

    /**
     * Create a container with some repositories.
     * 
     * @param FilesystemInterface $filesystem
     * @param string              $class
     */
    public static function __callStatic($name, $args)
    {
        $filesystem = array_shift($args);

        if (!($filesystem instanceof FilesystemInterface)) {
            throw new \InvalidArgumentException(sprintf('Invalid argument. Expected a %s but got %s', FilesystemInterface::class, gettype($filesystem)));
        }

        $class = __NAMESPACE__.'\\'.ucfirst($name);

        if (!class_exists($class)) {
            throw new BadMethodCallException(sprintf('The repository class "%s" does not exists', $class));
        }

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
     *
     * @return self
     */
    public function __set($name, Repository $repository)
    {
        $this->repositories[$name] = $repository;
    }
}
