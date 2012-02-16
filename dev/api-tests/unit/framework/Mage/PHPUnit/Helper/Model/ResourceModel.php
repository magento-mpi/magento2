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
