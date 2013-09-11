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

namespace Magento\Adminhtml\Block\Poll\Answer;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll_answer';
        $answerData = \Mage::getModel('\Magento\Poll\Model\Poll\Answer');
        if( $this->getRequest()->getParam($this->_objectId) ) {
            $answerData = \Mage::getModel('\Magento\Poll\Model\Poll\Answer')
                ->load($this->getRequest()->getParam($this->_objectId));
            \Mage::register('answer_data', $answerData);
        }

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/poll/edit', array('id' => $answerData->getPollId(), 'tab' => 'answers_section')) . '\');');
        $this->_updateButton('save', 'label', __('Save Answer'));
        $this->_updateButton('delete', 'label', __('Delete Answer'));
    }

    public function getHeaderText()
    {
        return __("Edit Answer '%1'", $this->escapeHtml(\Mage::registry('answer_data')->getAnswerTitle()));
    }

}
