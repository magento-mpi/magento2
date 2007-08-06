<?php
/**
 * Poll edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Poll_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll';

        $this->_updateButton('save', 'label', __('Save Poll'));
        $this->_updateButton('delete', 'label', __('Delete Poll'));

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $pollData = Mage::getModel('poll/poll')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('poll_data', $pollData);
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('poll_data') && Mage::registry('poll_data')->getId() ) {
            return __('Edit Poll') . " '" . Mage::registry('poll_data')->getPollTitle() . "'";
        } else {
            return __('New Poll');
        }
    }
}