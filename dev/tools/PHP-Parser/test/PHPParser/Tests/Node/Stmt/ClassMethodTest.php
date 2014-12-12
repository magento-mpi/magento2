<?php

class PHPParser_Tests_Node_Stmt_ClassMethodTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier)
    {
        $node = new PHPParser_Node_Stmt_ClassMethod('foo', [
            'type' => constant('PHPParser_Node_Stmt_Class::MODIFIER_' . strtoupper($modifier))
        ]);

        $this->assertTrue($node->{'is' . $modifier}());
    }

    /**
     * @dataProvider provideModifiers
     */
    public function testNoModifiers($modifier)
    {
        $node = new PHPParser_Node_Stmt_ClassMethod('foo', ['type' => 0]);

        $this->assertFalse($node->{'is' . $modifier}());
    }

    public function provideModifiers()
    {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['abstract'],
            ['final'],
            ['static'],
        ];
    }
}
