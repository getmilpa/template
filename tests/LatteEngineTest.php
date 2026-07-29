<?php

declare(strict_types=1);

namespace Milpa\Template\Tests;

use Milpa\Template\LatteEngine;
use PHPUnit\Framework\TestCase;

final class LatteEngineTest extends TestCase
{
    private string $cache;

    protected function setUp(): void
    {
        $this->cache = sys_get_temp_dir() . '/milpa-template-test-' . getmypid();
        @mkdir($this->cache, 0777, true);
    }

    public function testRendersSimpleTemplateWithParams(): void
    {
        $engine = new LatteEngine(__DIR__ . '/fixtures/', $this->cache);
        self::assertSame('<p>Hola Rod</p>', trim($engine->render('simple.latte', ['name' => 'Rod'])));
    }

    public function testRendersChildExtendingSharedLayoutViaMultiDirLoader(): void
    {
        $engine = new LatteEngine(__DIR__ . '/fixtures/', $this->cache);
        $engine->setViewPath(__DIR__ . '/fixtures/');
        self::assertSame('<html><body><h1>Milpa</h1></body></html>', trim($engine->render('child.latte', ['title' => 'Milpa'])));
    }
}
