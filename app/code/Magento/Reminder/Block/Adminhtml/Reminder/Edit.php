<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder rule edit form block
 */
namespace Magento\Reminder\Block\Adminhtml\Reminder;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Reminder data
     *
     * @var \Magento\Reminder\Helper\Data
     */
    protected $_reminderData = null;

    /**
     * @param \Magento\Reminder\Helper\Data $reminderData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Reminder\Helper\Data $reminderData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_reminderData = $reminderData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Run Now" button
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magento_Reminder';
        $this->_controller = 'adminhtml_reminder';

        parent::_construct();

        /** @var $rule Magento_Reminder_Model_Rule */
        $rule = $this->_coreRegistry->registry('current_reminder_rule');
        if ($rule && $rule->getId()) {
            $confirm = __('Are you sure you want to match this rule now?');
            if ($limit = $this->_reminderData->getOneRunLimit()) {
                $confirm .= ' ' . __('No more than %1 customers may receive the reminder email after this action.', $limit);
            }
            $this->_addButton('run_now', array(
                'label'   => __('Run Now'),
                'onclick' => "confirmSetLocation('{$confirm}', '{$this->getRunUrl()}')"
            ), -1);
        }

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
        $rule = $this->_coreRegistry->registry('current_reminder_rule');
        if ($rule->getRuleId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($rule->getName()));
        } else {
            return __('New Rule');
        }
    }

    /**
     * Get url for immediate run sending process
     *
     * @return string
     */
    public function getRunUrl()
    {
        $rule = $this->_coreRegistry->registry('current_reminder_rule');
        return $this->getUrl('*/*/run', array('id' => $rule->getRuleId()));
    }
}
