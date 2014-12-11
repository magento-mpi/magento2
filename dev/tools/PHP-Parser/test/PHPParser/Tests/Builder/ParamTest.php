<?php

class PHPParser_Tests_Builder_ParamTest extends PHPUnit_Framework_TestCase
{
    public function createParamBuilder($name)
    {
        return new PHPParser_Builder_Param($name);
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testDefaultValues($value, $expectedValueNode)
    {
        $node = $this->createParamBuilder('test')
            ->setDefault($value)
            ->getNode();

        $this->assertEquals($expectedValueNode, $node->default);
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

    public function testTypeHints()
    {
        $node = $this->createParamBuilder('test')
            ->setTypeHint('array')
            ->getNode();

        $this->assertEquals(
            new PHPParser_Node_Param('test', null, 'array'),
            $node
        );

        $node = $this->createParamBuilder('test')
            ->setTypeHint('callable')
            ->getNode();

        $this->assertEquals(
            new PHPParser_Node_Param('test', null, 'callable'),
            $node
        );

        $node = $this->createParamBuilder('test')
            ->setTypeHint('Some\Class')
            ->getNode();

        $this->assertEquals(
            new PHPParser_Node_Param('test', null, new PHPParser_Node_Name('Some\Class')),
            $node
        );
    }

    public function testByRef()
    {
        $node = $this->createParamBuilder('test')
            ->makeByRef()
            ->getNode();

        $this->assertEquals(
            new PHPParser_Node_Param('test', null, null, true),
            $node
        );
    }
}
