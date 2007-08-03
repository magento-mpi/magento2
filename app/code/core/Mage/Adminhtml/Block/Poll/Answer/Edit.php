<?php
/**
 * Admin poll answer edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Poll_Answer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll_answer';

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $answerData = Mage::getModel('poll/poll_answer')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('answer_data', $answerData);
        }

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::getUrl('*/poll/edit', array('id' => $answerData->getPollId(), 'tab' => 'answers_section')) . '\');');
        $this->_updateButton('save', 'label', __('Save Answer'));
        $this->_updateButton('delete', 'label', __('Delete Answer'));
    }

    public function getHeaderText()
    {
        return __('Edit Answer') . " '" . Mage::registry('answer_data')->getAnswerTitle() . "'";
    }

}
