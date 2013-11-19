<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class ClassFirst
{

}
class ClassSecond
{

}
class ClassThird
{

}
class ClassD
{

}
interface InterfaceFirst
{

}
class ImplementationOfInterfaceFirst implements InterfaceFirst
{

}
interface InterfaceSecond
{

}
class ImplementationOfInterfaceSecond implements InterfaceSecond
{

}
class ContextFirst implements \Magento\ObjectManager\ContextInterface
{
    public function __construct(
        \ClassFirst $exA, \ClassSecond $exB, \ClassThird $exC,
        \InterfaceFirst $interfaceA,
        \ImplementationOfInterfaceSecond $implOfBInterface
    ) {
        $this->_exA = $exA;
        $this->_exB = $exB;
        $this->_exC = $exC;
        $this->_interfaceA = $interfaceA;
        $this->_implOfBInterface = $implOfBInterface;
    }

}

class ClassArgumentAlreadyInjectedInContext
{
    public function __construct(\ContextFirst $context, \ClassFirst $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}

class ClassArgumentWithInterfaceImplementation
{
    public function __construct(\ContextFirst $context, \ImplementationOfInterfaceFirst $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}

class ClassArgumentWithInterface
{
    public function __construct(\ContextFirst $context, \InterfaceSecond $exB)
    {
        $this->_context = $context;
        $this->_exB = $exB;
    }
}

class ClassArgumentWithAlreadyInjectedInterface
{
    public function __construct(\ContextFirst $context, \InterfaceFirst $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}