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
 * Unit testing helper for Magento resource model objects.
 * Is a singleton.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Model_ResourceModel extends Mage_PHPUnit_Helper_Model_Model
{
    /**
     * Name of the pool with real resource model names
     *
     * @var string
     */
    protected $_resourceModelNamesPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_RESOURCE_MODEL_NAMES;

    /**
     * Returns pool of real resource model names
     *
     * @return Mage_PHPUnit_StaticDataPool_ResourceModelName
     */
    protected function _getResourceModelNamesPool()
    {
        return $this->_getStaticDataObject($this->_resourceModelNamesPool);
    }

    /**
     * Returns real model class name.
     *
     * @param string $modelName
     * @return string
     */
    public function getRealModelClass($modelName)
    {
        return parent::getRealModelClass($this->getResourceModelName($modelName));
    }

    /**
     * Returns real resource model name
     *
     * @param string $modelName Model name like 'catalog/product'
     * @return string Real model name like 'catalog/mysql4_product'
     */
    public function getResourceModelName($modelName)
    {
        $resourceModelName = $this->_getResourceModelNamesPool()->getResourceModelName($modelName);
        if (!$resourceModelName) {
            $resourceModelName = $this->_getResourceModelNameFromConfig($modelName);
            $this->_getResourceModelNamesPool()->setResourceModelName($modelName, $resourceModelName);
        }
        return $resourceModelName;
    }

    /**
     * Get real resource model name
     *
     * @param string $modelName
     * @return string
     */
    protected function _getResourceModelNameFromConfig($modelName)
    {
        $classArray = explode('/', $modelName);
        if (count($classArray) != 2) {
            return false;
        }

        list($module, $model) = $classArray;
        $moduleNode = Mage::getConfig()->getNode("global/models/{$module}");
        if (!$moduleNode) {
            return false;
        }

        if (!empty($moduleNode->resourceModel)) {
            $resourceModel = (string)$moduleNode->resourceModel;
        } else {
            return false;
        }

        return $resourceModel . '/' . $model;
    }

    /**
     * Rewrite model by delegator class.
     * You can rewrite one model only once for one test.
     *
     * @param string $model
     * @param string $className delegator class name
     */
    public function rewriteModelByClass($model, $className)
    {
        parent::rewriteModelByClass($this->getResourceModelName($model), $className);
    }
}
