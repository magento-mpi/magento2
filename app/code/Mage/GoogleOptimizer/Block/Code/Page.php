<?php
/**
 * Google Optimizer Page Block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Block_Code_Page extends Mage_GoogleOptimizer_Block_Code
{
    /**
     * @var Mage_Cms_Model_Page
     */
    protected $_page;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Cms_Model_Page $page
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_GoogleOptimizer_Helper_Data $helper,
        Mage_Core_Model_Registry $registry,
        Mage_Cms_Model_Page $page,
        array $data = array()
    )
    {
        $this->_page = $page;
        parent::__construct($context, $helper, $registry, $data);
    }

    /**
     * Return entity (product, category, cms page)
     *
     * @return Mage_Cms_Model_Page|Mage_Catalog_Model_Product|Mage_Catalog_Model_Category
     */
    protected function _getEntity()
    {
        return $this->_page;
    }
}
