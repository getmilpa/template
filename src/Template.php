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

/**
 * Orquestador delgado de rendering: delega en el {@see TemplateEngineInterface} inyectado. Sin acoplamiento
 * al host (ni DIContainer ni EventDispatcher): el evento legacy TEMPLATE_RENDER se retiró (cero listeners).
 */
final class Template
{
    public function __construct(private readonly TemplateEngineInterface $engine)
    {
    }

    /** Reapunta el directorio base de plantillas del motor envuelto. */
    public function setViewPath(string $path): void
    {
        $this->engine->setViewPath($path);
    }

    /**
     * Renderiza `$view` con `$params` y devuelve el HTML.
     *
     * @param array<string, mixed> $params
     */
    public function render(string $view, array $params = []): string
    {
        return $this->engine->render($view, $params);
    }
}
