<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class ClassA {}
class ClassB {}
class ClassC {}

interface InterfaceA {}
class ImplementationOfInterfaceA implements InterfaceA {}

interface InterfaceB {}
class ImplementationOfInterfaceB implements InterfaceB {}

class Context implements \Magento\ObjectManager\ContextInterface
{
    public function __construct(
        \ClassA $exA, \ClassB $exB, \ClassC $exC,
        \InterfaceA $interfaceA,
        \ImplementationOfInterfaceB $implOfBInterface
    ) {
        $this->_exA = $exA;
        $this->_exB = $exB;
        $this->_exC = $exC;
        $this->_interfaceA = $interfaceA;
        $this->_implOfBInterface = $implOfBInterface;
    }

}

class ClassArgumentAlreadyInjectedIntoContext
{
    public function __construct(\Context $context, \ClassA $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}

class ClassArgumentWrongOrderForParentArguments extends ClassArgumentAlreadyInjectedIntoContext
{
    public function __construct(\Context $context, \ClassA $exA, \ClassB $exB)
    {
        parent::__construct($exA, $context);
    }
}

class ClassArgumentWithOptionalParams
{
    public function __construct(\Context $context, array $data = array())
    {
    }
}

class ClassArgumentWithWrongParentArgumentsType extends ClassArgumentWithOptionalParams
{
    public function __construct(\Context $context, \ClassB $exB, \ClassC $exC, array $data = array())
    {
        parent::__construct($context, $exB);
    }
}