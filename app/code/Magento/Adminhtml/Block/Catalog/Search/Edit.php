<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tag edit block
 */
class Magento_Adminhtml_Block_Catalog_Search_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'catalog_search';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Search'));
        $this->_updateButton('delete', 'label', __('Delete Search'));
    }

    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_catalog_search')->getId()) {
            $queryText = $this->escapeHtml($this->_coreRegistry->registry('current_catalog_search')->getQueryText());
            return __("Edit Search '%1'", $queryText);
        } else {
            return __('New Search');
        }
    }
}
