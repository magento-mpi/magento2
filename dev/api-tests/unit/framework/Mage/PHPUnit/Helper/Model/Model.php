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
 * Unit testing helper for Magento models.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Model_Model extends Mage_PHPUnit_Helper_Model_Abstract
{
    /**
     * Name of the pool with model's real class names
     *
     * @var string
     */
    protected $_realModelClassesPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_REAL_MODEL_CLASSES;

    /**
     * Group type name
     *
     * @var string
     */
    protected $_group = 'model';

    /**
     * Returns pool of real model class names
     *
     * @return Mage_PHPUnit_StaticDataPool_ModelClass
     */
    protected function _getModelClassNamesPool()
    {
        return $this->_getStaticDataObject($this->_realModelClassesPool);
    }

    /**
     * Returns real model class name
     *
     * @param string $modelName
     * @return string
     */
    public function getRealModelClass($modelName)
    {
        $className = $this->_getModelClassNamesPool()->getRealModelClass($modelName);
        if (!$className) {
            $className = $this->_getModelClassNameFromConfig($modelName);
            $this->_getModelClassNamesPool()->setRealModelClass($modelName, $className);
        }
        return $className;
    }

    /**
     * Gets model's real class name from config object,
     * but does not add it to config object's cache array.
     * So, this method is safe for models rewriting.
     *
     * @param string $model
     * @return string
     */
    protected function _getModelClassNameFromConfig($model)
    {
        $config = Mage::getConfig()->getNode("global");

        if ($config && isset($config->rewrites->{$model})) {
            $className = (string)$config->rewrites->{$model};
        } else {
            $className = $model;
        }

        return $className;
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
        $nodePath = "global/rewrites/{$model}";
        if (Mage::getConfig()->getNode($nodePath) != $className) {
            Mage::getConfig()->setNode($nodePath, $className);
        }
    }
}
