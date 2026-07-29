<?php

/**
 * This file is part of Milpa Template — the framework-agnostic template rendering (Latte) layer of the Milpa PHP framework.
 *
 * (c) Rodrigo Vicente - TeamX Agency — https://teamx.agency <hola@teamx.agency>
 *
 * @license Apache-2.0
 *
 * @link    https://github.com/getmilpa/template
 */

declare(strict_types=1);

namespace Milpa\Template;

use Latte\Loaders\FileLoader;
use Latte\RuntimeException;

/**
 * Extends Latte FileLoader to allow template resolution across multiple base directories.
 *
 * Default FileLoader restricts templates to a single baseDir. This loader
 * allows plugin panel layouts to extend shared templates in resources/views/
 * without bypassing the security check entirely.
 */
class MultiDirFileLoader extends FileLoader
{
    /** @var list<string> Normalized extra allowed directories */
    private array $extraDirs = [];

    /**
     * @param string $baseDir      Primary base directory (plugin view path)
     * @param string ...$extraDirs Additional directories where templates may be loaded from
     */
    public function __construct(string $baseDir, string ...$extraDirs)
    {
        parent::__construct($baseDir);
        foreach ($extraDirs as $dir) {
            $this->extraDirs[] = self::normalizePath($dir . '/');
        }
    }

    /**
     * El contenido de la plantilla, buscada primero en el directorio propio y luego en el compartido.
     *
     * Ese orden es la regla entera: lo propio gana, y un layout compartido sólo aparece cuando nadie
     * lo sobrescribió. Al revés, el host no podría personalizar nada sin editar el plugin.
     */
    public function getContent(string $fileName): string
    {
        $file = $this->baseDir . $fileName;

        if ($this->baseDir !== null && !str_starts_with(self::normalizePath($file), $this->baseDir)) {
            $allowed = false;
            foreach ($this->extraDirs as $dir) {
                if (str_starts_with(self::normalizePath($file), $dir)) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                throw new RuntimeException("Template '$file' is not within the allowed path '{$this->baseDir}'.");
            }
        }

        if (!is_file($file)) {
            throw new RuntimeException("Missing template file '$file'.");
        }

        if ($this->isExpired($fileName, time())) {
            if (@touch($file) === false) {
                trigger_error(
                    "File's modification time is in the future. Cannot update it: " . error_get_last()['message'],
                    E_USER_WARNING
                );
            }
        }

        return file_get_contents($file);
    }
}
