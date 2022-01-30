<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use Exception;
use RuntimeException;

final class Container
{
    /**
     * Holds the list of cached Phony generator instances.
     *
     * @var array<string, object>
     */
    private array $instances = [];

    public function __construct()
    {
        $this->load();
    }

    /**
     * Returns a Phony generator instance for the given name.
     *
     * @param  string  $name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function get(string $name): mixed
    {
        if (! isset($this->instances[$name])) {
            throw new RuntimeException("Generator '$name' not found.");
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
     * Loads Phony generators from the cache file.
     *
     * @return void
     */
    public function load(): void
    {
        $cachedGenerators = getcwd().'/vendor/phonyland-generators.json';

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
                depth: 2,
                flags: JSON_THROW_ON_ERROR
            );

            foreach ($generatorClasses as $name => $class) {
                $this->instances[$name] = new $class();
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
