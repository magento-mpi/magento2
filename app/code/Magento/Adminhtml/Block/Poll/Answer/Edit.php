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
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Poll_Answer_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll_answer';
        $answerData = Mage::getModel('Mage_Poll_Model_Poll_Answer');
        if( $this->getRequest()->getParam($this->_objectId) ) {
            $answerData = Mage::getModel('Mage_Poll_Model_Poll_Answer')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('answer_data', $answerData);
        }

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/poll/edit', array('id' => $answerData->getPollId(), 'tab' => 'answers_section')) . '\');');
        $this->_updateButton('save', 'label', Mage::helper('Mage_Poll_Helper_Data')->__('Save Answer'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Poll_Helper_Data')->__('Delete Answer'));
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Poll_Helper_Data')->__("Edit Answer '%s'", $this->escapeHtml(Mage::registry('answer_data')->getAnswerTitle()));
    }

}
