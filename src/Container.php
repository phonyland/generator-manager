<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use Exception;
use Phonyland\Framework\Generator;
use Phonyland\Framework\Phony;
use RuntimeException;

final class Container
{
    /**
     * Holds the list of cached Phony generator instances.
     *
     * @var array<string, \Phonyland\Framework\Generator>
     */
    private array $instances = [];

    public function __construct(
        protected Phony $phony
    ) {
        $this->load();
    }

    /**
     * Returns a Phony generator instance for the given name.
     *
     * @param  string  $name
     *
     * @return Generator
     *
     * @throws \Exception
     */
    public function get(string $name): Generator
    {
        if (! isset($this->instances[$name])) {
            throw new RuntimeException("Phony Generator '$name' not found.");
        }

        return $this->instances[$name];
    }

    /**
     * Returns if a Phony generator instance exists for the given name.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->instances[$name]);
    }

    /**
     * Sets a generator instance to the container with an alias.
     *
     * @param  string                          $alias
     * @param  \Phonyland\Framework\Generator  $generator
     *
     * @return void
     */
    public function set(string $alias, Generator $generator): void
    {
        $this->instances[$alias] = $generator;
    }

    /**
     * Loads Phony generators from the cache file.
     *
     * @return void
     */
    public function load(): void
    {
        $cachedGenerators = getcwd() . '/vendor/phonyland-generators.json';

        if (! file_exists($cachedGenerators)) {
            return;
        }

        $content = file_get_contents($cachedGenerators);
        if ($content === false) {
            return;
        }

        try {
            $generatorClasses = json_decode(
                json: $content,
                associative: true,
                depth: 4,
                flags: JSON_THROW_ON_ERROR
            );

            foreach ($generatorClasses as $generator) {
                /** @var Generator $generatorInstance */
                $generatorInstance = new $generator['class'](
                    alias: $generator['alias'],
                    name: $generator['name'],
                    phony: $this->phony,
                );

                $generatorInstance->setDataPackages($generator['data'] ?? []);

                $this->instances[$generator['alias']] = $generatorInstance;
            }
        } catch (Exception) {
            return;
        }
    }

    /**
     * Resets the loaded Phony generators.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->instances = [];
    }

    /**
     * Resets and reloads the Phony generator instances.
     *
     * @return void
     */
    public function reload(): void
    {
        $this->reset();
        $this->load();
    }
}
