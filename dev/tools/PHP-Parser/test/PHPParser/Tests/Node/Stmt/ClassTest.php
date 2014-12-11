<?php

class PHPParser_Tests_Node_Stmt_ClassTest extends PHPUnit_Framework_TestCase
{
    public function testIsAbstract()
    {
        $class = new PHPParser_Node_Stmt_Class('Foo', ['type' => PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT]);
        $this->assertTrue($class->isAbstract());

        $class = new PHPParser_Node_Stmt_Class('Foo');
        $this->assertFalse($class->isAbstract());
    }

    public function testIsFinal()
    {
        $class = new PHPParser_Node_Stmt_Class('Foo', ['type' => PHPParser_Node_Stmt_Class::MODIFIER_FINAL]);
        $this->assertTrue($class->isFinal());

        $class = new PHPParser_Node_Stmt_Class('Foo');
        $this->assertFalse($class->isFinal());
    }

    public function testGetMethods()
    {
        $methods = [
            new PHPParser_Node_Stmt_ClassMethod('foo'),
            new PHPParser_Node_Stmt_ClassMethod('bar'),
            new PHPParser_Node_Stmt_ClassMethod('fooBar'),
        ];
        $class = new PHPParser_Node_Stmt_Class('Foo', [
            'stmts' => [
                new PHPParser_Node_Stmt_TraitUse([]),
                $methods[0],
                new PHPParser_Node_Stmt_Const([]),
                $methods[1],
                new PHPParser_Node_Stmt_Property(0, []),
                $methods[2],
            ]
        ]);

        $this->assertEquals($methods, $class->getMethods());
    }
}
