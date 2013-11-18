<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class ClassWithAllArgumentTypes
{
    const DEFAULT_VALUE = 'Const Value';

    /**
     * @param stdClass $stdClassObject
     * @param classWithoutConstruct $WithoutConstructorClassObject
     * @param $someVariable
     * @param string $const
     * @param int $optionalNumValue
     * @param string $optionalStringValue
     * @param array $optionalArrayValue
     */
    public function __construct(
        \stdClass $stdClassObject,
        \classWithoutConstruct $WithoutConstructorClassObject,
        $someVariable,
        $const = \ClassWithAllArgumentTypes::DEFAULT_VALUE,
        $optionalNumValue = 9807,
        $optionalStringValue = 'optional string',
        $optionalArrayValue = array('optionalKey' => 'optionalValue')
    ) {

    }
}

class ClassWithoutOwnConstruct extends ClassWithAllArgumentTypes
{

}

class ClassWithoutConstruct
{

}

class ClassExtendsDefaultPhpType extends \RuntimeException
{

}

class FirstClassForParentCall
{
    /**
     * @param stdClass $stdClassObject
     * @param ClassExtendsDefaultPhpType $runeTimeException
     * @param array $arrayVariable
     */
    public function __construct(
       \stdClass $stdClassObject,
       \ClassExtendsDefaultPhpType $runeTimeException,
       $arrayVariable = array('key' => 'value'))
   {
   }
}

class ThirdClassForParentCall extends firstClassForParentCall
{
    /**
     * @param stdClass $stdClassObject
     * @param ClassExtendsDefaultPhpType $secondClass
     */
    public function __construct(
        \stdClass $stdClassObject,
        \ClassExtendsDefaultPhpType $secondClass
    )
    {
        parent::__construct($stdClassObject, $secondClass);
    }
}

class WrongArgumentsOrder extends firstClassForParentCall
{
    /**
     * @param stdClass $stdClassObject
     * @param ClassExtendsDefaultPhpType $secondClass
     */
    public function __construct(
        \stdClass $stdClassObject,
        \ClassExtendsDefaultPhpType $secondClass
    )
    {
        parent::__construct($secondClass, $stdClassObject);
    }
}

class ArgumentsOnSeparateLines extends firstClassForParentCall
{
    /**
     * @param stdClass $stdClassObject
     * @param ClassExtendsDefaultPhpType $secondClass
     */
    public function __construct(
        \stdClass $stdClassObject,
        \ClassExtendsDefaultPhpType $secondClass
    )
    {
        parent::__construct(
            $secondClass,
            $stdClassObject
        );
    }
}