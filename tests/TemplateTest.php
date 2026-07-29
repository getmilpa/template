<?php

declare(strict_types=1);

namespace Milpa\Template\Tests;

use Milpa\Template\LatteEngine;
use Milpa\Template\Template;
use PHPUnit\Framework\TestCase;

final class TemplateTest extends TestCase
{
    public function testDelegatesRenderToInjectedEngine(): void
    {
        $cache = sys_get_temp_dir() . '/milpa-template-tpl-' . getmypid();
        @mkdir($cache, 0777, true);
        $template = new Template(new LatteEngine(__DIR__ . '/fixtures/', $cache));
        self::assertSame('<p>Hola Ana</p>', trim($template->render('simple.latte', ['name' => 'Ana'])));
    }
}
