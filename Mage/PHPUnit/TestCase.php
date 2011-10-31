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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Main TestCase class for Magento unit tests.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Is called before test will be run.
     */
    protected function setUp()
    {
        $this->prepareInitializers();
        $this->runInitializers();
        $this->checkModuleEnabled();
    }

    /**
     * Prepares initializers.
     * Is called in setUp method first. Can be overridden in testCases to add more initializers.
     */
    protected function prepareInitializers()
    {
        Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_App');
    }

    /**
     * Runs initializers.
     * Is called in setUp method.
     */
    protected function runInitializers()
    {
        Mage_PHPUnit_Initializer_Factory::run();
    }

    /**
     * Mark a test as skipped if its module is disabled
     */
    protected function checkModuleEnabled()
    {
        $moduleHelper = Mage_PHPUnit_Helper_Factory::getHelper('module');
        $moduleName = $moduleHelper->getModuleNameByClass($this);
        if ($moduleHelper->isModuleDisabled($moduleName)) {
            $this->markTestSkipped("Module '{$moduleName}' is disabled");
        }
    }

    /**
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->cleanPoolsData();
        Mage_PHPUnit_Initializer_Factory::cleanInitializers();
    }

    /**
     * Helper method. Can be needed to reset cache manually.
     */
    protected function resetCache()
    {
        Mage_PHPUnit_Cache_Memory::staticClean();
    }

    /**
     * Returns mock builder for model.
     * Needed to create a mock for Mage::getModel(...) calls
     *
     * @param string $modelName like 'catalog/product'
     * @return Mage_PHPUnit_MockBuilder_Model_Model
     */
    protected function getModelMockBuilder($modelName)
    {
        return new Mage_PHPUnit_MockBuilder_Model_Model($this, $modelName);
    }

    /**
     * Returns mock builder for resource model.
     * Needed to create a mock for Mage::getResourceModel(...) calls
     *
     * @param string $modelName Resource model name like 'catalog/product'
     * @return Mage_PHPUnit_MockBuilder_Model_ResourceModel
     */
    protected function getResourceModelMockBuilder($modelName)
    {
        return new Mage_PHPUnit_MockBuilder_Model_ResourceModel($this, $modelName);
    }

    /**
     * Returns mock builder for singleton models.
     * Needed to create a mock for Mage::getSingleton(...) calls
     *
     * @param string $modelName like 'catalog/product'
     * @return Mage_PHPUnit_MockBuilder_Model_Singleton
     */
    protected function getSingletonMockBuilder($modelName)
    {
        return new Mage_PHPUnit_MockBuilder_Model_Singleton($this, $modelName);
    }

    /**
     * Returns mock builder for resource singleton models.
     * Needed to create a mock for Mage::getResourceSingleton(...) calls
     *
     * @param string $modelName like 'catalog/product'
     * @return Mage_PHPUnit_MockBuilder_Model_ResourceSingleton
     */
    protected function getResourceSingletonMockBuilder($modelName)
    {
        return new Mage_PHPUnit_MockBuilder_Model_ResourceSingleton($this, $modelName);
    }

    /**
     * Returns mock builder for helper.
     * Needed to create a mock for Mage::helper(...) calls
     *
     * @param string $helperName like 'catalog'
     * @return Mage_PHPUnit_MockBuilder_Model_Helper
     */
    protected function getHelperMockBuilder($helperName)
    {
        return new Mage_PHPUnit_MockBuilder_Model_Helper($this, $helperName);
    }

    /**
     * Returns mock builder for Mage::dispatchEvent(...) construction
     *
     * @param string $eventName
     * @return Mage_PHPUnit_MockBuilder_Event
     */
    protected function getEventMockBuilder($eventName)
    {
        return new Mage_PHPUnit_MockBuilder_Event($this, $eventName);
    }

    /**
     * Returns mock builder for block.
     *
     * @param string $blockName
     * @return Mage_PHPUnit_MockBuilder_Model_Block
     */
    protected function getBlockMockBuilder($blockName)
    {
        return new Mage_PHPUnit_MockBuilder_Model_Block($this, $blockName);
    }

    /**
     * Clean all data from static data pools.
     */
    protected function cleanPoolsData()
    {
        Mage_PHPUnit_StaticDataPoolContainer::getInstance()->clean();
    }

    /**
     * Get model helper.
     * Needed to get real model's class name.
     *
     * @return Mage_PHPUnit_Helper_Model_Model
     */
    protected function _getModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_model');
    }

    /**
     * Get resource model helper.
     * Needed to get real resource model's class name.
     *
     * @return Mage_PHPUnit_Helper_Model_ResourceModel
     */
    protected function _getResourceModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_resourceModel');
    }

    /**
     * Get helper model for Magento Helpers.
     * Needed to get real helper's class name.
     *
     * @return Mage_PHPUnit_Helper_Model_Helper
     */
    protected function _getHelperModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_helper');
    }

    /**
     * Get helper model for Magento Blocks.
     * Needed to get real block's class name.
     *
     * @return Mage_PHPUnit_Helper_Model_Block
     */
    protected function _getBlockHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_block');
    }

    /**
     * Get helper for Magento stores.
     *
     * @return Mage_PHPUnit_Helper_Store
     */
    protected function _getStoreHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('store');
    }

    /**
     * Get event helper.
     *
     * @return Mage_PHPUnit_Helper_Event
     */
    protected function _getEventHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('event');
    }

    /**
     * Returns model's className.
     * Does not save it into Config's cache array.
     *
     * @param string $model
     * @return string
     */
    protected function getModelClassName($model)
    {
        return $this->_getModelHelper()->getRealModelClass($model);
    }

    /**
     * Returns Resource model's className.
     * Does not save it into Config's cache array.
     *
     * @param string $model
     * @return string
     */
    protected function getResourceModelClassName($model)
    {
        return $this->_getResourceModelHelper()->getRealModelClass($model);
    }

    /**
     * Returns helper's className.
     *
     * @param string $helper
     * @return string
     */
    protected function getHelperClassName($helper)
    {
        return $this->_getHelperModelHelper()->getRealModelClass($helper);
    }

    /**
     * Returns block's className.
     *
     * @param string $block
     * @return string
     */
    protected function getBlockClassName($block)
    {
        return $this->_getBlockHelper()->getRealModelClass($block);
    }

    /**
     * Sets config data for store.
     * Can be needed to get your value in Mage::getStoreConfig()
     *
     * @param string $path
     * @param string $value
     * @param int|null|Mage_Core_Model_Store $store Non null value will work only if Magento is installed
     */
    protected function setStoreConfig($path, $value, $store = null)
    {
        $this->_getStoreHelper()->setStoreConfig($path, $value, $store);
    }

    /**
     * Remove event observers from config
     *
     * @param array $eventNames - remove observers for events from this array. if array is empty, remove for all events
     * @return Mage_PHPUnit_TestCase
     */
    protected function disableObservers($eventNames = array())
    {
        $this->_getEventHelper()->disableObservers($eventNames);
        return $this;
    }
}
