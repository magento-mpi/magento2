<?php
/**
 * Google Optimizer Product Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_Googleoptimizer
    extends Magento_GoogleOptimizer_Block_Adminhtml_TabAbstract
{
    /**
     * Get Product entity
     *
     * @return Magento_Catalog_Model_Product
     * @throws RuntimeException
     */
    protected function _getEntity()
    {
        $entity = $this->_registry->registry('product');
        if (!$entity) {
            throw new RuntimeException('Entity is not found in registry.');
        }
        return $entity;
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Product View Optimization');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Product View Optimization');
    }
}
