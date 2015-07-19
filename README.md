# Yet Another CSS Minifier

YACM is a lightweight, but effective, CSS minifier.  It is slightly different from other minifiers in that it preserves the approximate vertical structure of the original CSS - so unless the formatting of the input is very unusual, the output should be just about readable.

The code is unit-tested and can be installed using Composer.

The code is based on [a Gist of mine](https://gist.github.com/danbettles/5781842), which was, itself, based on a script I wrote many years ago.

## Installation

Install using [Composer](https://getcomposer.org/).

```sh
composer require danbettles/yacm:dev-master
```

## Usage

```php
use Danbettles\Yacm\Minifier as CssMinifier;

$minifiedCss = CssMinifier::minifyNow('body { font-size: 1em; }');

//Or

$cssMinifier = new CssMinifier();
$minifiedCss = $cssMinifier->minify('body { font-size: 1em; }');
```
