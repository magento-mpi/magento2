<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Target rule edit form block
 */

namespace Magento\TargetRule\Block\Adminhtml\Targetrule;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected $_blockGroup = 'Magento_TargetRule';
    protected $_controller = 'adminhtml_targetrule';

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
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => __('Save and Continue Edit'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 3);
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = $this->_coreRegistry->registry('current_target_rule');
        if ($rule && $rule->getRuleId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($rule->getName()));
        } else {
            return __('New Rule');
        }
    }
}
