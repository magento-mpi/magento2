<?php

class PHPParser_Tests_Builder_PropertyTest extends PHPUnit_Framework_TestCase
{
    public function createPropertyBuilder($name)
    {
        return new PHPParser_Builder_Property($name);
    }

    public function testModifiers()
    {
        $node = $this->createPropertyBuilder('test')
            ->makePrivate()
            ->makeStatic()
            ->getNode();

        $this->assertEquals(
            new PHPParser_Node_Stmt_Property(
                PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE
              | PHPParser_Node_Stmt_Class::MODIFIER_STATIC,
                [
                    new PHPParser_Node_Stmt_PropertyProperty('test')
                ]
            ),
            $node
        );

        $node = $this->createPropertyBuilder('test')
            ->makeProtected()
            ->getNode();

        $this->assertEquals(
            new PHPParser_Node_Stmt_Property(
                PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED,
                [
                    new PHPParser_Node_Stmt_PropertyProperty('test')
                ]
            ),
            $node
        );

        $node = $this->createPropertyBuilder('test')
            ->makePublic()
            ->getNode();

        $this->assertEquals(
            new PHPParser_Node_Stmt_Property(
                PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC,
                [
                    new PHPParser_Node_Stmt_PropertyProperty('test')
                ]
            ),
            $node
        );
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testDefaultValues($value, $expectedValueNode)
    {
        $node = $this->createPropertyBuilder('test')
            ->setDefault($value)
            ->getNode();

        $this->assertEquals($expectedValueNode, $node->props[0]->default);
    }

    public function provideTestDefaultValues()
    {
        return [
            [
                null,
                new PHPParser_Node_Expr_ConstFetch(new PHPParser_Node_Name('null')),
            ],
            [
                true,
                new PHPParser_Node_Expr_ConstFetch(new PHPParser_Node_Name('true'))
            ],
            [
                false,
                new PHPParser_Node_Expr_ConstFetch(new PHPParser_Node_Name('false'))
            ],
            [
                31415,
                new PHPParser_Node_Scalar_LNumber(31415)
            ],
            [
                3.1415,
                new PHPParser_Node_Scalar_DNumber(3.1415)
            ],
            [
                'Hallo World',
                new PHPParser_Node_Scalar_String('Hallo World')
            ],
            [
                [1, 2, 3],
                new PHPParser_Node_Expr_Array([
                    new PHPParser_Node_Expr_ArrayItem(new PHPParser_Node_Scalar_LNumber(1)),
                    new PHPParser_Node_Expr_ArrayItem(new PHPParser_Node_Scalar_LNumber(2)),
                    new PHPParser_Node_Expr_ArrayItem(new PHPParser_Node_Scalar_LNumber(3)),
                ])
            ],
            [
                ['foo' => 'bar', 'bar' => 'foo'],
                new PHPParser_Node_Expr_Array([
                    new PHPParser_Node_Expr_ArrayItem(
                        new PHPParser_Node_Scalar_String('bar'),
                        new PHPParser_Node_Scalar_String('foo')
                    ),
                    new PHPParser_Node_Expr_ArrayItem(
                        new PHPParser_Node_Scalar_String('foo'),
                        new PHPParser_Node_Scalar_String('bar')
                    ),
                ])
            ],
            [
                new PHPParser_Node_Scalar_DirConst(),
                new PHPParser_Node_Scalar_DirConst()
            ]
        ];
    }
}
