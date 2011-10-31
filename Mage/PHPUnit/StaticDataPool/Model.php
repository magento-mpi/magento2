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
 * Class for pool of internal data needed for models mocking.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_StaticDataPool_Model extends Mage_PHPUnit_StaticDataPool_Abstract
{
    /**
     * Mocked models array =
     *     array('catalog/product' => array(MockObject1, MockObject2, ...), ...)
     *
     * @var array
     */
    protected $_mockedModels = array();

    /**
     * Mocked models, which were created as singletons, array =
     *  array('catalog/product' => MockObject, ...)
     *
     * @var array
     */
    protected $_mockedModelsAsSingletons = array();

    /**
     * Array of delegator class names for models.
     *  array('catalog/product' => 'MockDelegator_Mage_Catalog_Model_Product_acea23d3', ...)
     *
     * @var array
     */
    protected $_delegators = array();

    /**
     * Get delegator's class name for model.
     *
     * @param string $model
     * @return string
     */
    public function getDelegatorClass($model)
    {
        if (empty($this->_delegators[$model])) {
            return false;
        }
        return $this->_delegators[$model];
    }

    /**
     * Set delegator's class name for model.
     *
     * @param string $model
     * @param string $delegatorClassName
     * @return Mage_PHPUnit_StaticDataPool_MockBuilder_Model
     */
    public function setDelegatorClass($model, $delegatorClassName)
    {
        $this->_delegators[$model] = $delegatorClassName;
        return $this;
    }

    /**
     * Adds mock object to queue array.
     * Needed for delegators to get right mock object after each Mage::getModel() call.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     * @param string $model
     * @param bool $asSingleton
     */
    public function addMockToQueue($mock, $model, $asSingleton = false)
    {
        if ($asSingleton) {
            $this->_mockedModelsAsSingletons[$model] = $mock;
            unset($this->_mockedModels[$model]);
        } else {
            if (!isset($this->_mockedModels[$model])) {
                $this->_mockedModels[$model] = array();
            }
            $this->_mockedModels[$model][] = $mock;
        }
    }

    /**
     * Gets real mock object for delegator from $mockModels array.
     *
     * @param string $model
     * @param array $constructorArgs
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Abstract|object
     */
    public function getMockObject($model, $constructorArgs = array())
    {
        if (!empty($this->_mockedModelsAsSingletons[$model])) {
            return $this->_mockedModelsAsSingletons[$model];
        }

        if (!empty($this->_mockedModels[$model])) {
            return array_shift($this->_mockedModels[$model]);
        }
        //create native model otherwise
        $realClass = Mage_PHPUnit_StaticDataPoolContainer::getStaticDataObject(Mage_PHPUnit_StaticDataPoolContainer::POOL_REAL_MODEL_CLASSES)
            ->getRealModelClass($model);
        if (!$realClass) {
            throw new Exception('Cannot find real model class name in ModelClass static pool');
        }
        return new $realClass($constructorArgs);
    }
}
