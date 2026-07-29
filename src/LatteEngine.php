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

use Latte\Engine;
use Latte\Loaders\FileLoader;

/**
 * El motor real: {@see TemplateEngineInterface} implementado sobre Latte.
 *
 * Resuelve plantillas contra un directorio base y, cuando hay uno de layouts compartidos, contra los
 * dos — para que la vista de un plugin pueda extender un layout del host sin que el host tenga que
 * copiarlo a su lado. Ver {@see MultiDirFileLoader}.
 */
final class LatteEngine implements TemplateEngineInterface
{
    private Engine $engine;
    private string $viewPath;
    private readonly string $sharedViewsDir;

    /**
     * @param string $viewPath       Base directory for template resolution (ej: <root>/resources/views/).
     * @param string $cacheDir       Latte compiled-template cache directory (ej: <root>/storage/cache).
     * @param string $sharedViewsDir Directorio de layouts compartidos que las vistas de plugin pueden extender
     *                               (default: $viewPath). Reemplaza el hard-coded rootPath.'/resources/views'.
     */
    public function __construct(string $viewPath, string $cacheDir, string $sharedViewsDir = '')
    {
        $this->viewPath = $viewPath;
        $this->sharedViewsDir = $sharedViewsDir !== '' ? $sharedViewsDir : rtrim($viewPath, '/');
        $this->engine = new Engine();
        $this->engine->setTempDirectory($cacheDir);
        $this->engine->setLoader(new FileLoader($this->viewPath));
    }

    /**
     * Reapunta el directorio base de plantillas ya construido el motor.
     *
     * Existe porque un host descubre dónde viven sus vistas después de arrancar —al cargar plugins,
     * al leer configuración—, no en el constructor. Cambiarlo reinstala el loader: el anterior
     * seguiría resolviendo contra la ruta vieja y fallaría con «template not found» sobre archivos
     * que sí existen, que es de los errores más caros de leer.
     */
    public function setViewPath(string $path): void
    {
        $this->viewPath = $path;
        // MultiDirFileLoader deja que las vistas de plugin extiendan layouts compartidos.
        $this->engine->setLoader(new MultiDirFileLoader($this->viewPath, $this->sharedViewsDir));
    }

    /**
     * Renderiza `$template` con `$params` y devuelve el HTML, sin escribir a la salida.
     *
     * Devolver en vez de imprimir es lo que deja probar el resultado y componerlo dentro de otra
     * respuesta; un motor que hace `echo` obliga a capturar buffers a todo el que lo use.
     *
     * @param array<string, mixed> $params
     */
    public function render(string $template, array $params = []): string
    {
        return $this->engine->renderToString($template, $params);
    }
}
