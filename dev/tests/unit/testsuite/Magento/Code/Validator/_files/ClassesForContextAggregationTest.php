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
class ClassD {}

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

class ClassArgumentWithInterfaceImplementation
{
    public function __construct(\Context $context, \ImplementationOfInterfaceA $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}

class ClassArgumentWithInterface
{
    public function __construct(\Context $context, \InterfaceB $exB)
    {
        $this->_context = $context;
        $this->_exB = $exB;
    }
}

class ClassArgumentWithAlreadyInjectedInterface
{
    public function __construct(\Context $context, \InterfaceA $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}