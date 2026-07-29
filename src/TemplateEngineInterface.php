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
 * A pluggable view-rendering backend (e.g. Latte, Twig, plain PHP).
 */
interface TemplateEngineInterface
{
    /** Sets the base directory the engine resolves template paths against. */
    public function setViewPath(string $path): void;

    /**
     * Renders the given template with the supplied variables and returns the resulting output.
     *
     * @param array<string, mixed> $params
     */
    public function render(string $template, array $params = []): string;
}
