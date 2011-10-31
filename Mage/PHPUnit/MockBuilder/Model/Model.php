<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Class which creates mock object for models when they are created
 * in a code using Mage::getModel('...');
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Model_Model extends Mage_PHPUnit_MockBuilder_Model_Abstract
{
    /**
     * Global counter of delegator objects
     *
     * @var int
     */
    static protected $_delegatorNumber = 0;

    /**
     * Delegators pool key
     *
     * @var string
     */
    protected $_delegatorsPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_MODEL_DELEGATORS;

    /**
     * Additional setting for MockBuilder.
     * Allows to make one mock object for all Mage::getModel() for concrete model.
     *
     * @var bool
     */
    protected $_asSingleton = false;

    /**
     * Gets 'asSingleton' setting value
     *
     * @return bool
     */
    public function getAsSingleton()
    {
        return $this->_asSingleton;
    }

    /**
     * Sets 'asSingleton' setting value.
     * Allows to make one mock object for all Mage::getModel() for concrete model.
     *
     * @param bool $asSingleton
     */
    public function setAsSingleton($asSingleton)
    {
        $this->_asSingleton = $asSingleton;
        return $this;
    }

    /**
     * Returns PHPUnit model helper.
     *
     * @return Mage_PHPUnit_Helper_Model_Model
     */
    protected function _getModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_model');
    }

    /**
     * Prepares builder's state or do some actions before calling $this->getMock()
     */
    protected function _prepareMock()
    {
        $this->_getModelHelper()->rewriteModelByClass($this->getModel(), $this->_getDelegatorClass());
        $this->className = $this->getRealModelClass();
    }

    /**
     * Method which is called after getMock() method.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _afterGetMock($mock)
    {
        $this->_addMockToQueue($mock);
    }

    /**
     * Returns pool with delegators data.
     *
     * @return Mage_PHPUnit_StaticDataPool_Model
     */
    protected function _getPoolOfDelegators()
    {
        return $this->_getStaticDataObject($this->_delegatorsPool);
    }

    /**
     * Get delegator's class name for model or init it if needed.
     *
     * @return string
     */
    protected function _getDelegatorClass()
    {
        $delegatorClass = $this->_getPoolOfDelegators()->getDelegatorClass($this->getModel());
        if (!$delegatorClass) {
            $delegatorClass = $this->_createDelegator();
            $this->_getPoolOfDelegators()->setDelegatorClass($this->getModel(), $delegatorClass);
        }
        return $delegatorClass;
    }

    /**
     * Returns new delegator's class name
     *
     * @param string $modelClass
     * @return string
     */
    protected function _generateMockDelegatorClassName($modelClass)
    {
        return "MockDelegator_{$modelClass}_".(self::$_delegatorNumber++);
    }

    /**
     * Init delegator class name and its realization in memory for model.
     *
     * @return string delegator class name
     */
    protected function _createDelegator()
    {
        $modelClass = $this->getRealModelClass();
        $delegatorClass = $this->_generateMockDelegatorClassName($modelClass);
        if (!class_exists($delegatorClass)) {
            $delegatorClassText =
            "class {$delegatorClass} extends {$modelClass}
            {
                protected \$___mockObj___;
                public function __construct(\$args = array())
                {
                    \$this->___mockObj___ = Mage_PHPUnit_StaticDataPoolContainer::getStaticDataObject('{$this->_delegatorsPool}')->getMockObject('{$this->getModel()}', \$args);
                }
                public function __call(\$method, \$args)
                {
                    return call_user_func_array(array(\$this->___mockObj___, \$method), \$args);
                }";
            $reflection = new ReflectionClass($modelClass);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (!$method->isConstructor() &&
                    !$method->isStatic() &&
                    !$method->isAbstract() &&
                    !$method->isDestructor() &&
                    !$method->isFinal() &&
                    $method->getName() != '__call'
                ) {
                    $delegatorClassText .=
                    "public function {$method->getName()}(".
                        PHPUnit_Util_Class::getMethodParameters($method).
                    ") {
                        \$args = func_get_args();
                        return call_user_func_array(array(\$this->___mockObj___, '{$method->getName()}'), \$args);
                    }";
                }
            }
            $delegatorClassText .= "}";

            eval($delegatorClassText);
        }
        return $delegatorClass;
    }

    /**
     * Adds mock object to queue array.
     * Needed for delegators to get right mock object after each Mage::getModel() call.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _addMockToQueue($mock)
    {
        $this->_getPoolOfDelegators()->addMockToQueue($mock, $this->getModel(), $this->getAsSingleton());
    }
}
