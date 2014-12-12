<?php

class PHPParser_Tests_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestPlaceholderReplacement
     * @covers PHPParser_Template
     */
    public function testPlaceholderReplacement($templateCode, $placeholders, $expectedPrettyPrint)
    {
        $parser = new PHPParser_Parser(new PHPParser_Lexer());
        $prettyPrinter = new PHPParser_PrettyPrinter_Default();

        $template = new PHPParser_Template($parser, $templateCode);
        $this->assertEquals(
            $expectedPrettyPrint,
            $prettyPrinter->prettyPrint($template->getStmts($placeholders))
        );
    }

    public function provideTestPlaceholderReplacement()
    {
        return [
            [
                '<?php $__name__ + $__Name__;',
                ['name' => 'foo'],
                '$foo + $Foo;',
            ],
            [
                '<?php $__name__ + $__Name__;',
                ['Name' => 'Foo'],
                '$foo + $Foo;'
            ],
            [
                '<?php $__name__ + $__Name__;',
                ['name' => 'foo', 'Name' => 'Bar'],
                '$foo + $Bar;'
            ],
            [
                '<?php $__name__ + $__Name__;',
                ['Name' => 'Bar', 'name' => 'foo'],
                '$foo + $Bar;'
            ],
            [
                '<?php $prefix__Name__Suffix;',
                ['name' => 'infix'],
                '$prefixInfixSuffix;'
            ],
            [
                '<?php $___name___;',
                ['name' => 'foo'],
                '$_foo_;'
            ],
            [
                '<?php $foobar;',
                [],
                '$foobar;'
            ],
        ];
    }
}
