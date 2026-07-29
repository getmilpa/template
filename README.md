<p align="center">
  <a href="https://github.com/getmilpa">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/getmilpa/core/main/art/lockup/milpa-lockup-v-color-dark.svg">
      <img src="https://raw.githubusercontent.com/getmilpa/core/main/art/lockup/milpa-lockup-v-color-light.svg" alt="Milpa" width="300">
    </picture>
  </a>
</p>

# Milpa Template

> Framework-agnostic template rendering for the Milpa PHP framework — [Latte](https://latte.nette.org/)
> behind an interface small enough to replace.

[![CI](https://github.com/getmilpa/template/actions/workflows/ci.yml/badge.svg)](https://github.com/getmilpa/template/actions/workflows/ci.yml)
[![Packagist](https://img.shields.io/packagist/v/milpa/template.svg)](https://packagist.org/packages/milpa/template)
[![PHP](https://img.shields.io/badge/php-%E2%89%A5%208.3-777bb4.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-Apache--2.0-blue.svg)](LICENSE)

Two methods — point at a directory, render a file with variables. Everything else the package does
exists to solve one problem: **a plugin's view has to be able to extend a layout the host owns,
without either of them copying files to the other's side.**

## Install

```bash
composer require milpa/template
```

## Usage

```php
use Milpa\Template\LatteEngine;
use Milpa\Template\Template;

$engine = new LatteEngine(
    viewPath: __DIR__ . '/resources/views',
    cacheDir: __DIR__ . '/storage/cache',
    sharedViewsDir: __DIR__ . '/resources/views',   // layouts every plugin may extend
);

echo (new Template($engine))->render('pages/home.latte', ['title' => 'Hello']);
```

## The one idea

`MultiDirFileLoader` resolves a template against **its own directory first, and the shared one
second**. That order is the whole rule:

- a plugin ships `views/mail/receipt.latte` and it wins for that plugin;
- the same plugin writes `{extends 'layouts/base.latte'}` and gets the **host's** layout, because it
  is not in the plugin's own directory;
- the host overrides a plugin view by placing a file with the same name in its own directory — no
  registration, no config, no editing the plugin.

Reversed, a host could not customize anything without editing the package it installed.

## What it deliberately does not do

**No globals, no `rootPath`.** Paths arrive through the constructor. A renderer that reads a global
constant works in exactly one application and fails silently in tests, which is where you find out.

**No `echo`.** `render()` returns the string. A renderer that writes to output forces every caller to
capture buffers and makes the result impossible to compose into another response.

**No template functions or filters of its own.** The Latte engine is exposed as it is; conveniences
belong to whoever knows the domain.

## Replacing Latte

`TemplateEngineInterface` is two methods. If you want Twig, or plain PHP, or something you wrote
yourself, implement those two and pass it to `Template` — nothing else in the package knows Latte
exists. That is the reason the interface is there and the reason it stayed small.

## Requirements

PHP ≥ 8.3 · [latte/latte](https://latte.nette.org/) ^3.0 · [PSR-3](https://www.php-fig.org/psr/psr-3/)
for optional logging.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Security reports go through
[SECURITY.md](SECURITY.md) — privately, via GitHub Security Advisories.

## License

[Apache-2.0](LICENSE) © Rodrigo Vicente - TeamX Agency.

---

Milpa is designed, built, and maintained by **[Rodrigo Vicente - TeamX Agency](https://teamx.agency/?utm_source=github&utm_medium=readme&utm_campaign=milpa&utm_content=template)**.
