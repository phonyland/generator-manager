<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use JsonException;
use Phonyland\Framework\Container;

/**
 * @internal
 */
final class Loader
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
     * @var array<int, object>
     */
    private static array $instances = [];

    /**
     * Returns an array of phony generators to execute.
     *
     * @param  string  $interface  the interface for the hook to execute
     *
     * @return array<int, object> list of generators
     *
     * @throws \ReflectionException
     */
    public static function getGenerators(string $interface): array
    {
        return array_values(
            array_filter(
                self::getGeneratorInstances(),
                function ($generator) use ($interface): bool {
                    return $generator instanceof $interface;
                }
            )
        );
    }

    public static function reset(): void
    {
        self::$loaded = false;
        self::$instances = [];
    }

    /**
     * Returns the list of generators instances.
     *
     * @return array<int, object>
     *
     * @throws \ReflectionException
     */
    private static function getGeneratorInstances(): array
    {
        if (! self::$loaded) {
            $cachedGenerators = sprintf('%s/vendor/phonyland-generators.json', getcwd());
            $container = Container::getInstance();

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
                    associative: false,
                    depth: 1024,
                    flags: JSON_THROW_ON_ERROR
                );
            } catch (JsonException) {
                $generatorClasses = [];
            }

            self::$instances = array_map(
                callback: static fn ($class) => $container->get($class),
                array: $generatorClasses
            );

            self::$loaded = true;
        }

        return self::$instances;
    }
}
