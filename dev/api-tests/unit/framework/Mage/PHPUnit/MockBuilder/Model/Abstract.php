<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for constructions (like Mage::getModel(), Mage::helper())
 * of creating model-like Magento objects
 * to create mock objects for them.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_MockBuilder_Model_Abstract extends Mage_PHPUnit_MockBuilder_Abstract
{
    /**
     * Model name
     *
     * @var string
     */
    protected $_model;

    /**
     * Returns PHPUnit model helper.
     *
     * @return Mage_PHPUnit_Helper_Model_Abstract
     */
    abstract protected function _getModelHelper();

    /**
     * Constructor
     *
     * @param PHPUnit_Framework_TestCase $testCase
     * @param string $model
     */
    public function __construct(PHPUnit_Framework_TestCase $testCase, $model)
    {
        $this->testCase  = $testCase;
        $this->_model = $model;
        $this->className = $model;
    }

    /**
     * Returns model name
     *
     * @return string
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Prepares builder's state or do some actions before calling $this->getMock()
     */
    protected function _prepareMock()
    {
        $this->className = $this->getRealModelClass();
    }

    /**
     * Returns real model's class name
     *
     * @return string
     */
    public function getRealModelClass()
    {
        return $this->_getModelHelper()->getRealModelClass($this->getModel());
    }
}
