<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Danbettles\Yacm;

/**
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */
class Minifier
{
    /**
     * Removes superfluous whitespace from the specified CSS; enough newlines are left, however, to preserve the basic
     * vertical structure of the CSS, to make it just about readable after minification.
     *
     * @param string $css
     * @return string
     */
    public function removeSuperfluousWhitespaceFilter($css)
    {
        //Normalize newlines.
        $css = str_replace(["\r\n", "\r", "\n"], "\n", $css);

        //Normalize horizontal whitespace.
        $css = str_replace("\t", ' ', $css);

        //Replace multiple, contiguous occurrences of the same whitespace character with just one.
        //We don't simply replace all whitespace characters with spaces - for example - because we want to retain the
        //vertical structure of the CSS, so that it's just about readable after minification.
        $css = preg_replace('/([\n ])\1+/', '$1', $css);

        //Remove horizontal whitespace from around delimiters.
        $css = preg_replace('/[ ]*([,:;\{\}])[ ]*/', '$1', $css);

        //Remove leading and trailing whitespace from lines.
        $css = preg_replace('/^[ ]*(.*?)[ ]*$/m', '$1', $css);

        //Remove empty lines.
        $css = trim(preg_replace('/(?<=\n)[ ]*\n|/', '', $css));

        return $css;
    }

    /**
     * Removes comments from the specified CSS.
     *
     * @param string $css
     * @return string
     */
    public function removeCommentsFilter($css)
    {
        return preg_replace('{\/\*(.*?)\*\/}s', '', $css);
    }

    /**
     * Removes units from zero values.
     *
     * A zero value with units, no matter what unit of measurement is used, always equates to zero ("0"), so the units
     * are a waste of space.
     *
     * @param string $css
     * @return string
     */
    public function removeUnitsFromZeroesFilter($css)
    {
        //See http://www.w3.org/TR/CSS21/grammar.html#scanner and http://www.w3schools.com/cssref/css_units.asp
        return preg_replace('/\b0((?:em|ex|ch|rem|vw|vh|vmin|vm|vmax|cm|mm|in|px|pt|pc)\b|%)/i', '0', $css);
    }

    /**
     * If possible, replaces hex colours with their condensed equivalents.
     *
     * @param string $css
     * @return string
     */
    public function condenseHexColoursFilter($css)
    {
        $hexByte = '[\da-fA-F]';
        return preg_replace("/#({$hexByte})\\1({$hexByte})\\2({$hexByte})\\3/", '#$1$2$3', $css);
    }

    /**
     * Minifies the specified CSS.
     *
     * @param string $css
     * @return string
     */
    public function minify($css)
    {
        $css = $this->removeCommentsFilter($css);
        $css = $this->removeUnitsFromZeroesFilter($css);
        $css = $this->condenseHexColoursFilter($css);
        $css = $this->removeSuperfluousWhitespaceFilter($css);
        return $css;
    }

    /**
     * Minifies the specified CSS in a single method call.
     *
     * @param string $css
     * @return string
     */
    public static function minifyNow($css)
    {
        $minifier = new self();
        return $minifier->minify($css);
    }
}
