<?php
/**
 * Google Optimizer Page Block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Block_Code_Page extends Magento_GoogleOptimizer_Block_CodeAbstract
{
    /**
     * @var Mage_Cms_Model_Page
     */
    protected $_page;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_GoogleOptimizer_Helper_Data $helper
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_GoogleOptimizer_Helper_Code $codeHelper
     * @param Mage_Cms_Model_Page $page
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_GoogleOptimizer_Helper_Data $helper,
        Magento_Core_Model_Registry $registry,
        Magento_GoogleOptimizer_Helper_Code $codeHelper,
        Mage_Cms_Model_Page $page,
        array $data = array()
    ) {
        // Mage_Cms_Model_Page is singleton
        $this->_page = $page;
        parent::__construct($context, $helper, $registry, $codeHelper, $data);
    }

    /**
     * Get cms page entity
     *
     * @return Mage_Cms_Model_Page
     */
    protected function _getEntity()
    {
        return $this->_page;
    }
}
