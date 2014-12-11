<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;


/**
 * Class LineBreakTest
 *
 * This class is used to test the breaking down of lines to appropriate length results.
 */
class LineBreakTest extends TestBase
{
    /**
     * This method tests the array breaking.
     *
     * @dataProvider dataArrays
     */
    public function testArrays(array $tokens, $level, $result)
    {
        $this->runTokenTest($tokens, $level, $result, 'Array line was not split as expected.');
    }

    public function dataArrays()
    {
        /*
         * array(x1,x2,x3x)
         * array(x
         * 1,x
         * 2,x
         * 3x
         * )
         *
         * 1 blank	HardIndent
         * 2 space	HardIndent
         * 3 space	HardIndent
         * 4 blank  Hard
         */
        $lineBreak = new CallLineBreak();
        $arrayAlpha = [
            'array(',
            $lineBreak,
            '1',
            ',',
            $lineBreak,
            '2',
            ',',
            $lineBreak,
            '3',
            $lineBreak,
            ')',
            new HardLineBreak(),
        ];

        return [
            [$arrayAlpha, 0, "array(1, 2, 3)\n"],
            [$arrayAlpha, 1, "array(\n1,\n2,\n3\n)\n"],
            [$arrayAlpha, 2, "array(\n1,\n2,\n3\n)\n"]
        ];
    }

    /**
     * This method tests the class implements breaking.
     *
     * @dataProvider dataClassImplements
     */
    public function testClassImplements(array $tokens, $level, $result)
    {
        $this->runTokenTest($tokens, $level, $result, 'Class line was not split as expected.');
    }

    public function dataClassImplements()
    {
        /*
                class alpha extends beta implementsxi1,xi2,xi3
                {

                class alpha extends beta implementsx
           i1,x
           i2,x
           i3
                {

                1	blank	\n
                2	blank	\n
                3	blank	\n
        */
        $lineBreak = new ClassInterfaceLineBreak();
        $classAlpha = [
            'class ',
            'alpha',
            ' extends ',
            'beta',
            ' implements',
            $lineBreak,
            'i1',
            ',',
            $lineBreak,
            'i2',
            ',',
            $lineBreak,
            'i3',
            new HardLineBreak(),
            '{',
            new HardLineBreak(),
        ];

        $classBeta = [
            'class ',
            'beta',
            ' implements',
            $lineBreak,
            'i1',
            new HardLineBreak(),
            '{',
            new HardLineBreak(),
        ];

        return [
            [$classAlpha, 0, "class alpha extends beta implements i1, i2, i3\n{\n"],
            [$classAlpha, 1, "class alpha extends beta implements\ni1,\ni2,\ni3\n{\n"],
            [$classAlpha, 2, "class alpha extends beta implements\ni1,\ni2,\ni3\n{\n"],
            [$classBeta, 0, "class beta implements i1\n{\n"],
            [$classBeta, 1, "class beta implements\ni1\n{\n"],
            [$classBeta, 2, "class beta implements\ni1\n{\n"]
        ];
    }

    /**
     * This method tests the generic conditional line breaks.
     * @param array $tokens
     * @param $level
     * @param $result
     *
     * @dataProvider dataConditionalBreaks
     */
    public function testConditionalBreaks(array $tokens, $level, $result)
    {
        $this->runTokenTest($tokens, $level, $result, 'Conditional line was not split as expected.');
    }

    public function dataConditionalBreaks()
    {
        $lineBreak = new SimpleListLineBreak();
        $constAlpha = [
            'const ',
            $lineBreak,
            'AlPHA',
            ' = ',
            '\'a\'',
            ',',
            $lineBreak,
            'BETA',
            ' = ',
            '\'b\'',
            ',',
            $lineBreak,
            'GAMMA',
            ' = ',
            '\'c\'',
            ';',
            new HardLineBreak(),
        ];

        $constNumber = ['const ', $lineBreak, 'ONE', ' = ', '\'1\'', ';', new HardLineBreak()];

        return [
            [$constAlpha, 0, "const AlPHA = 'a', BETA = 'b', GAMMA = 'c';\n"],
            [$constAlpha, 1, "const AlPHA = 'a',\nBETA = 'b',\nGAMMA = 'c';\n"],
            [$constAlpha, 2, "const AlPHA = 'a',\nBETA = 'b',\nGAMMA = 'c';\n"],
            [$constNumber, 0, "const ONE = '1';\n"],
            [$constNumber, 1, "const ONE = '1';\n"],
            [$constNumber, 2, "const ONE = '1';\n"],
            [["HEREDOC", new HardConditionalLineBreak(new LineBreakCondition(';')), ';'], 0, "HEREDOC;"],
            [
                ["HEREDOC", new HardConditionalLineBreak(new LineBreakCondition(';')), ',"other")'],
                0,
                "HEREDOC\n,\"other\")"
            ],
            [
                [
                    "HEREDOC",
                    new HardConditionalLineBreak(new HeredocTerminatingLineCondition()),
                    new CallLineBreak(),
                    ');',
                ],
                1,
                "HEREDOC\n);"
            ]
        ];
    }

    /**
     * This method tests the parameter line breaking.
     *
     * @dataProvider dataMethodParameters
     */
    public function testMethodParameters(array $tokens, $level, $result)
    {
        $this->runTokenTest($tokens, $level, $result, 'Function line was not split as expected.');
    }

    public function dataMethodParameters()
    {
        $lineBreak = new ParameterLineBreak();
        $functionAlpha = [
            'public ',
            'function ',
            'alpha',
            '(',
            $lineBreak,
            'TestClass',
            ' ',
            '$',
            'a',
            ',',
            $lineBreak,
            'TestClass',
            ' ',
            '$',
            'b',
            ',',
            $lineBreak,
            'TestClass',
            ' ',
            '$',
            'c',
            ',',
            $lineBreak,
            'TestClass',
            ' ',
            '$',
            'd',
            $lineBreak,
            ')',
            $lineBreak,
            '{',
            new HardLineBreak(),
        ];
        $functionBeta = [
            'private ',
            'function ',
            'beta',
            '(',
            $lineBreak,
            'TestClass',
            ' ',
            '$',
            'a',
            $lineBreak,
            ')',
            $lineBreak,
            '{',
            new HardLineBreak(),
        ];
        $functionGamma = ['protected ', 'function ', 'gamma', '(', ')', $lineBreak, '{', new HardLineBreak()];

        return [
            [
                $functionAlpha,
                0,
                "public function alpha(TestClass \$a, TestClass \$b, TestClass \$c, TestClass \$d)\n{\n",
            ],
            [
                $functionAlpha,
                1,
                "public function alpha(\nTestClass \$a,\nTestClass \$b,\nTestClass \$c,\nTestClass \$d\n) {\n"
            ],
            [
                $functionAlpha,
                2,
                "public function alpha(\nTestClass \$a,\nTestClass \$b,\nTestClass \$c,\nTestClass \$d\n) {\n"
            ],
            [$functionBeta, 0, "private function beta(TestClass \$a)\n{\n"],
            [$functionBeta, 1, "private function beta(\nTestClass \$a\n) {\n"],
            [$functionBeta, 2, "private function beta(\nTestClass \$a\n) {\n"],
            [$functionGamma, 0, "protected function gamma()\n{\n"],
            [$functionGamma, 1, "protected function gamma()\n{\n"],
            [$functionGamma, 2, "protected function gamma()\n{\n"]
        ];
    }

    /**
     * This method returns the array of tokens as a line.
     * @param array $tokens Array of tokens to be turned into a line.
     * @return Line
     */
    private function getLine(array $tokens)
    {
        $line = new Line();
        foreach ($tokens as $token) {
            $line->add($token);
        }
        return $line;
    }

    /**
     * This method tests the passed in token for the given level. The results are tested.
     * @param array $tokens
     * @param $level
     * @param $expected
     * @param $message
     */
    private function runTokenTest(array $tokens, $level, $expected, $message)
    {
        $line = $this->getLine($tokens);
        $this->assertNotNull($line);
        $actualResult = '';
        /** @var Line $currentLine */
        foreach ($line->splitLine($level) as $currentLine) {
            $actualResult .= $currentLine->getLine();
        }
        $this->assertEquals($expected, $actualResult, $message);
    }
}
