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
 * Adminhtml tax rule Edit Container
 */

namespace Magento\Adminhtml\Block\Tax\Rule;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
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

    /**
     * Init class
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'rule';
        $this->_controller = 'tax_rule';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Rule'));
        $this->_updateButton('delete', 'label', __('Delete Rule'));

        $this->_addButton('save_and_continue', array(
            'label'     => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 10);
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('tax_rule')->getId()) {
            return __("Edit Rule");
        } else {
            return __('New Rule');
        }
    }
}
