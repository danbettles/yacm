<?php
/**
 * @copyright Copyright (c) 2015, Dan Bettles
 * @license http://www.opensource.org/licenses/MIT MIT
 * @author Dan Bettles <danbettles@yahoo.co.uk>
 */

namespace Tests\Danbettles\Yacm\Minifier;

use Danbettles\Yacm\Minifier;

class Test extends \PHPUnit_Framework_TestCase
{
    public function testIsInstantiable()
    {
        new Minifier();
    }

    public static function providesCssWithCommentsRemoved()
    {
        return [
            [
                '',
                '',
            ],
            [
                '',
                '/*Foo*/',
            ],
            [
                "\nbody {\n    font-family: sans-serif;\n}",
                "/*Foo*/\nbody {\n    font-family: sans-serif;\n}",
            ],
            [
                "body {\n    \n    font-family: sans-serif;\n}",
                "body {\n    /*Foo*/\n    font-family: sans-serif;\n}",
            ],
            [
                "body {\n    font-family: sans-serif;  \n}",
                "body {\n    font-family: sans-serif;  /*Foo*/\n}",
            ],
            [
                '',
                '/**#@+Something*//**#@-Something*/',
            ],
        ];
    }

    /**
     * @dataProvider providesCssWithCommentsRemoved
     */
    public function testRemovecommentsfilterRemovesComments($expectedOutput, $input)
    {
        $minifier = new Minifier();
        $actualOutput = $minifier->removeCommentsFilter($input);

        $this->assertSame($expectedOutput, $actualOutput);
    }

    public static function providesCssWithSuperfluousWhitespaceRemoved()
    {
        return [
            [
                '',
                '',
            ],
            [
                "body{\nfont-family:sans-serif;\nfont-size:1em;\ncolor:#000;\n}",
                "body {\r\nfont-family: sans-serif;\rfont-size: 1em;\ncolor: #000;\n}"
            ],
            [
                "body{\nfont-family:sans-serif;\nfont-size:1em;\ncolor:#000;\n}",
                "body {\n    font-family: sans-serif;\n    font-size: 1em;\n    color: #000;\n}",
            ],
            [
                "body{\nfont-family:sans-serif;\nfont-size:1em;\ncolor:#000;\n}",
                "body {\n\tfont-family: sans-serif;\n\tfont-size: 1em;\n\tcolor: #000;\n}",
            ],
            [
                'body{font-family:sans-serif;}',
                ' body {font-family: sans-serif;} ',
            ],
            [
                "body{\nfont-family:sans-serif;\n}",
                "body {\n    font-family: sans-serif;\n}",
            ],
            [
                "body{\nfont-family:sans-serif;\n}\np{\nline-height:1em;\n}",
                "body {\n    font-family: sans-serif;\n}\n\np {\n    line-height: 1em;\n}",
            ],
            [
                'body{font-family:sans-serif;}',
                "body {font-family: sans-serif;}\n",
            ],
            [
                "body{\nfont-family:sans-serif;\n}\np{\nline-height:1em;\n}",
                "body {\n    font-family: sans-serif;\n}\n    \np {\n    line-height: 1em;\n}",
            ],
            [
                "h1,h2{\nfont-weight:bold;\n}",
                "h1, h2 {\n    font-weight: bold;\n}",
            ],
            [
                "h1,h2{font-weight:bold;}",
                "h1 , h2 { font-weight : bold ; }",
            ],
        ];
    }

    /**
     * @dataProvider providesCssWithSuperfluousWhitespaceRemoved
     */
    public function testRemovesuperfluouswhitespacefilterRemovesSuperfluousWhitespace($expectedOutput, $input)
    {
        $minifier = new Minifier();
        $actualOutput = $minifier->removeSuperfluousWhitespaceFilter($input);

        $this->assertSame($expectedOutput, $actualOutput);
    }

    public static function providesCssContainingZeroesWithUnitsRemoved()
    {
        return [
            [
                '',
                '',
            ],
            [
                '0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0',
                '0em 0ex 0ch 0rem 0vw 0vh 0vmin 0vm 0vmax 0% 0cm 0mm 0in 0px 0pt 0pc',
            ],
            [
                '.smallest { font-size: 0; }',
                '.smallest { font-size: 0em; }',
            ],
        ];
    }

    /**
     * @dataProvider providesCssContainingZeroesWithUnitsRemoved
     */
    public function testRemoveunitsfromzeroesfilterRemovesUnitsFromZeroes($expectedOutput, $input)
    {
        $minifier = new Minifier();
        $actualOutput = $minifier->removeUnitsFromZeroesFilter($input);

        $this->assertSame($expectedOutput, $actualOutput);
    }

    public static function providesCssContainingHexColoursThatHaveBeenCondensed()
    {
        return [
            [
                '',
                '',
            ],
            [
                '#fff #abc #123456',
                '#ffffff #aabbcc #123456',
            ],
            [
                'body { color: #000; }',
                'body { color: #000000; }',
            ],
        ];
    }

    /**
     * @dataProvider providesCssContainingHexColoursThatHaveBeenCondensed
     */
    public function testCondensehexcoloursfilterCondensesHexColours($expectedOutput, $input)
    {
        $minifier = new Minifier();
        $actualOutput = $minifier->condenseHexColoursFilter($input);

        $this->assertSame($expectedOutput, $actualOutput);
    }

    public static function providesCssThatHasBeenMinified()
    {
        return [
            [
                '',
                '',
            ],
            [
                <<<END
body{
color:#000;
}
h1,h2{font-weight:bold;}
.smallest{
font-size:0;
}
END
                ,
                <<<END
body {
    color : #000000 ;  /*Colour value will be condensed.*/
}
    /*This empty line will be removed.*/
h1, h2 { font-weight: bold; }

.smallest {
    font-size: 0em;
}
END
                ,
            ],
        ];
    }

    /**
     * @dataProvider providesCssThatHasBeenMinified
     */
    public function testMinifyMinifiesTheSpecifiedCssUsingAllFilters($expectedOutput, $input)
    {
        $minifier = new Minifier();
        $actualOutput = $minifier->minify($input);

        $this->assertSame($expectedOutput, $actualOutput);
    }

    public function testMinifynowMinifiesTheSpecifiedCssInASingleMethodCall()
    {
        $minifiedCss = Minifier::minifyNow('body { font-size: 1em; }');

        $this->assertSame('body{font-size:1em;}', $minifiedCss);
    }
}
