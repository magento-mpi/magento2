<?php

class PHPParser_Tests_BuilderFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestFactory
     */
    public function testFactory($methodName, $className)
    {
        $factory = new PHPParser_BuilderFactory();
        $this->assertInstanceOf($className, $factory->$methodName('test'));
    }

    public function provideTestFactory()
    {
        return [
            ['class',     'PHPParser_Builder_Class'],
            ['interface', 'PHPParser_Builder_Interface'],
            ['method',    'PHPParser_Builder_Method'],
            ['function',  'PHPParser_Builder_Function'],
            ['property',  'PHPParser_Builder_Property'],
            ['param',     'PHPParser_Builder_Param'],
        ];
    }
}
