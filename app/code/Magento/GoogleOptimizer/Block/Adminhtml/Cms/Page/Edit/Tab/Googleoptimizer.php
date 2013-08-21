<?php
/**
 * Google Optimizer Cms Page Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Tab_Googleoptimizer
    extends Magento_GoogleOptimizer_Block_Adminhtml_TabAbstract
{
    /**
     * Get cms page model
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function _getEntity()
    {
        $entity = $this->_registry->registry('cms_page');
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
        return __('Page View Optimization');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Page View Optimization');
    }
}
