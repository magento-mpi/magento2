<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace TypeDuplication;

interface ArgumentInterface
{

}

class ArgumentBaseClass
{

}

class ArgumentClassOne extends ArgumentBaseClass
{

}

class ValidClassWithTheSameInterfaceTypeArguments
{
    protected $argumentOne;
    protected $argumentTwo;
    protected $argumentThree;

    public function __construct(
        ArgumentInterface $argumentOne,
        ArgumentClassOne $argumentTwo,
        ArgumentInterface $argumentThree
    ) {
        $this->argumentOne = $argumentOne;
        $this->argumentTwo = $argumentTwo;
        $this->argumentThree = $argumentThree;
    }
}

class ValidClassWithSubTypeArguments
{
    protected $argumentOne;
    protected $argumentTwo;
    protected $argumentThree;

    public function __construct(
        ArgumentBaseClass $argumentOne,
        ArgumentClassOne $argumentTwo,
        ArgumentInterface $argumentThree
    ) {
        $this->argumentOne = $argumentOne;
        $this->argumentTwo = $argumentTwo;
        $this->argumentThree = $argumentThree;
    }
}

class ValidClassWithSuppressWarnings
{
    protected $argumentOne;
    protected $argumentTwo;
    protected $argumentThree;

    /**
     * @SuppressWarnings(Magento.TypeDuplication)
     */
    public function __construct(
        ArgumentBaseClass $argumentOne,
        ArgumentBaseClass $argumentTwo,
        ArgumentInterface $argumentThree
    ) {
        $this->argumentOne = $argumentOne;
        $this->argumentTwo = $argumentTwo;
        $this->argumentThree = $argumentThree;
    }
}

class ValidClassWithDuplicatedTypes
{
    protected $argumentOne;
    protected $argumentTwo;
    protected $argumentThree;

    public function __construct(
        ArgumentBaseClass $argumentOne,
        ArgumentBaseClass $argumentTwo,
        ArgumentInterface $argumentThree
    ) {
        $this->argumentOne = $argumentOne;
        $this->argumentTwo = $argumentTwo;
        $this->argumentThree = $argumentThree;
    }
}

