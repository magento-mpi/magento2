<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Assigned theme list
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Assigned
    extends Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
{
    /**
     * Store manager model
     *
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get list title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Themes Assigned to Store Views');
    }

    /**
     * Add theme buttons
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Assigned
     */
    protected function _addThemeButtons($themeBlock)
    {
        parent::_addThemeButtons($themeBlock);
        $this->_addDuplicateButtonHtml($themeBlock);
        if (count($this->_storeManager->getStores()) > 1) {
            $this->_addAssignButtonHtml($themeBlock);
        }
        $this->_addEditButtonHtml($themeBlock);
        return $this;
    }
}
