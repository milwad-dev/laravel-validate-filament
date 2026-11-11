<?php

namespace Milwad\LaravelValidateFilament\Translation;

use Illuminate\Contracts\Translation\Loader;

class ValidateTranslationLoader implements Loader
{
    /**
     * The translation group used by the laravel-validate rules.
     */
    public const GROUP = 'validate';

    /**
     * Bundled locale directories, keyed by normalized locale (e.g. "it" => "/.../lang/It").
     *
     * @var array<string, string>|null
     */
    protected ?array $locales = null;

    public function __construct(protected Loader $loader, protected string $path) {}

    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string|null  $namespace
     * @return array<string, mixed>
     */
    public function load($locale, $group, $namespace = null): array
    {
        $lines = $this->loader->load($locale, $group, $namespace);

        if ($group !== self::GROUP || ($namespace !== null && $namespace !== '*')) {
            return $lines;
        }

        return array_replace_recursive($this->bundledLines($locale), $lines);
    }

    /**
     * The lines bundled with milwad/laravel-validate for the given locale.
     *
     * @return array<string, mixed>
     */
    public function bundledLines(string $locale): array
    {
        $directory = $this->locales()[$this->normalize($locale)] ?? null;

        if ($directory === null || ! is_file($file = $directory.'/'.self::GROUP.'.php')) {
            return [];
        }

        $lines = require $file;

        return is_array($lines) ? $lines : [];
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint): void
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    /**
     * Add a new JSON path to the loader.
     */
    public function addJsonPath($path): void
    {
        $this->loader->addJsonPath($path);
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array<string, string>
     */
    public function namespaces(): array
    {
        return $this->loader->namespaces();
    }

    /**
     * The decorated loader.
     */
    public function getLoader(): Loader
    {
        return $this->loader;
    }

    /**
     * Forward other calls, such as FileLoader::addPath(), to the decorated loader.
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->loader->{$method}(...$parameters);
    }

    /**
     * Map each bundled locale directory by its normalized name, so "it" finds "It" and "pt-BR" finds "pt_BR".
     *
     * @return array<string, string>
     */
    protected function locales(): array
    {
        if ($this->locales !== null) {
            return $this->locales;
        }

        $this->locales = [];

        foreach (glob($this->path.'/*', GLOB_ONLYDIR) ?: [] as $directory) {
            $this->locales[$this->normalize(basename($directory))] = $directory;
        }

        return $this->locales;
    }

    /**
     * Normalize a locale name for comparison.
     */
    protected function normalize(string $locale): string
    {
        return strtolower(str_replace('-', '_', $locale));
    }
}
