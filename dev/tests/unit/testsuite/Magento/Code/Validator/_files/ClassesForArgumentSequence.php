<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace ArgumentSequence;

class Context implements \Magento\ObjectManager\ContextInterface
{

}

class Required
{

}

class ParentClass
{
    /**
     * @var Context
     */
    protected $_context;

    /**
     * @var mixed
     */
    protected $_param;

    /**
     * @var null
     */
    protected $_areaCode;

    /**
     * @param Context $context
     * @param mixed $param
     * @param null $areaCode
     */
    public function __construct(Context $context, $param, $areaCode = null)
    {
        $this->_context = $context;
        $this->_param = $param;
        $this->_areaCode = $areaCode;
    }
}

class ValidChildClass extends ParentClass
{
    /**
     * @var mixed
     */
    protected $_required;

    /**
     * @var null
     */
    protected $_optional;

    /**
     * @param Context $context
     * @param mixed $param
     * @param null $required
     * @param null $areaCode
     * @param null $optional
     */
    public function __construct(Context $context, $param, $required, $areaCode = null, $optional = null)
    {
        $this->_required = $required;
        $this->_optional = $optional;
        parent::__construct($context, $param, $areaCode);
    }
}

class InvalidChildClassOne extends ParentClass
{
    /**
     * @var mixed
     */
    protected $_required;

    /**
     * @var null
     */
    protected $_optional;

    /**
     * @param mixed $required
     * @param Context $context
     * @param null $param
     * @param null $areaCode
     * @param null $optional
     */
    public function __construct($required, Context $context, $param, $areaCode = null, $optional = null)
    {
        $this->_required = $required;
        $this->_optional = $optional;
        parent::__construct($context, $param, $areaCode);
    }
}

class InvalidChildClassTwo extends ParentClass
{
    /**
     * @var mixed
     */
    protected $_required;

    /**
     * @var null
     */
    protected $_optional;

    /**
     * @param Context $context
     * @param mixed $required
     * @param null $param
     * @param null $areaCode
     * @param null $optional
     */
    public function __construct(Context $context, $required, $param, $areaCode = null, $optional = null)
    {
        $this->_required = $required;
        $this->_optional = $optional;
        parent::__construct($context, $param, $areaCode);
    }
}