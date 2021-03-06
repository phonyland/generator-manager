<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Phonyland\GeneratorManager\Commands\DumpCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class Manager implements PluginInterface, EventSubscriberInterface, Capable
{
    public const GENERATOR_CACHE_FILE = 'phonyland-generators.json';

    private Composer $composer;

    // @phpstan-ignore-next-line
    private IOInterface $io;

    /**
     * @inheritdoc
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @inheritdoc
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $vendorDirectory = $composer->getConfig()->get('vendor-dir');
        $generatorCacheFile = $vendorDirectory . '/' . self::GENERATOR_CACHE_FILE;

        if (file_exists($generatorCacheFile)) {
            unlink($generatorCacheFile);
        }
    }

    /**
     * @throws \Exception
     */
    public function registerGenerators(): void
    {
        $cmd = new DumpCommand();
        $cmd->setComposer($this->composer);
        $cmd->run(
            input: new ArrayInput([]),
            output: new ConsoleOutput(verbosity: OutputInterface::VERBOSITY_NORMAL, decorated: true)
        );
    }

    public static function getSubscribedEvents(): array
    {
        return ['post-autoload-dump' => 'registerGenerators'];
    }

    public function getCapabilities(): array
    {
        return [CommandProvider::class => PhonyCommandProvider::class];
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // no need to deactivate anything
    }
}
