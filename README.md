# Yet Another CSS Minifier

YACM is a lightweight, but effective, CSS minifier.  It is slightly different from other minifiers in that it preserves the approximate vertical structure of the original CSS - so unless the formatting of the input is very unusual, the output should be just about readable.

The code is unit-tested and can be installed using Composer.

This code is based on [a Gist of mine](https://gist.github.com/danbettles/5781842).

## Basic Usage

```php
use Danbettles\Yacm\Minifier as CssMinifier;

//@todo Load the library.

$minifiedCss = CssMinifier::minifyNow('body { font-size: 1em; }');

//Or

$cssMinifier = new CssMinifier();
$minifiedCss = $cssMinifier->minify('body { font-size: 1em; }');
```

## Use with BundleFu

You can minify the CSS bundled by [BundleFu](https://github.com/dotsunited/BundleFu) by calling YACM in a `CallbackFilter` filter.

```php
//@todo Create bundle as per Dots United's instructions.

$minifyCssFilter = new \DotsUnited\BundleFu\Filter\CallbackFilter(function($content) {
    return \Danbettles\Yacm\Minifier::minifyNow($content);
});

$bundle->setCssFilter($minifyCssFilter);

//@todo Continue using bundle as per Dots United's instructions.
```

If you need to add multiple CSS filters to your bundle then [follow Dots United's instructions on creating and using a filter chain](https://github.com/dotsunited/BundleFu#filters).

## Installation

Install using [Composer](https://getcomposer.org/).

```sh
composer require danbettles/yacm:dev-master
```
