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
 * Admin poll answer edit block
 */
class Magento_Adminhtml_Block_Poll_Answer_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll_answer';
        $answerData = Mage::getModel('Magento_Poll_Model_Poll_Answer');
        if ($this->getRequest()->getParam($this->_objectId)) {
            $answerData = Mage::getModel('Magento_Poll_Model_Poll_Answer')
                ->load($this->getRequest()->getParam($this->_objectId));
            $this->_coreRegistry->register('answer_data', $answerData);
        }

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/poll/edit', array(
            'id' => $answerData->getPollId(),
            'tab' => 'answers_section'
        )) . '\');');
        $this->_updateButton('save', 'label', __('Save Answer'));
        $this->_updateButton('delete', 'label', __('Delete Answer'));
    }

    public function getHeaderText()
    {
        $title = $this->escapeHtml($this->_coreRegistry->registry('answer_data')->getAnswerTitle());
        return __("Edit Answer '%1'", $title);
    }
}
