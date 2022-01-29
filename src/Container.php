<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use JsonException;

final class Container
{
    /**
     * Determines if the generator cache file was loaded.
     *
     * @var bool
     */
    private static bool $loaded = false;

    /**
     * Holds the list of cached generator instances.
     *
     * @var array<string, object>
     */
    private static array $instances = [];

    /**
     * Returns an array of phony generator instances to execute.
     *
     * @return array<string, object> a list of generators
     */
    public static function getGenerators(): array
    {
        if (! self::$loaded) {
            $cachedGenerators = getcwd().'/vendor/phonyland-generators.json';

            if (! file_exists($cachedGenerators)) {
                return [];
            }

            $content = file_get_contents($cachedGenerators);
            if ($content === false) {
                return [];
            }

            try {
                $generatorClasses = json_decode(
                    json: $content,
                    associative: true,
                    depth: 2,
                    flags: JSON_THROW_ON_ERROR
                );
            } catch (JsonException) {
                $generatorClasses = [];
            }

            foreach ($generatorClasses as $name => $class) {
                self::$instances[$name] = new $class();
            }

            self::$loaded = true;
        }

        return self::$instances;
    }

    /**
     * Resets the loaded generators.
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$loaded = false;
        self::$instances = [];
    }
}
