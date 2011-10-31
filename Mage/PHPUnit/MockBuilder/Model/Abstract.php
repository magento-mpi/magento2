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
