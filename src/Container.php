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

    /**
     * Holds the list of data packages for the generators.
     *
     * @var array<string, string>
     */
    private array $dataPackages = [];

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
                $this->instances[$generator['alias']] = new $generator['class']($this->phony);

                if (! isset($this->dataPackages[$generator['alias']])) {
                    $this->dataPackages[$generator['alias']] = [];
                }

                $this->dataPackages[$generator['alias']] += $generator['data'] ?? [];
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
        $this->dataPackages = [];
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
