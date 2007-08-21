<?php
/**
 * Review edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'review';

        $this->_updateButton('save', 'label', __('Save Review'));
        $this->_updateButton('delete', 'label', __('Delete Review'));

        if( $this->getRequest()->getParam('productId', false) ) {
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::getUrl('*/catalog_product/edit', array('id' => $this->getRequest()->getParam('productId', false))) .'\')' );
        }

        if( $this->getRequest()->getParam('customerId', false) ) {
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::getUrl('*/customer/edit', array('id' => $this->getRequest()->getParam('customerId', false))) .'\')' );
        }

        if( $this->getRequest()->getParam('ret', false) == 'pending' ) {
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::getUrl('*/*/pending') .'\')' );
            $this->_updateButton('delete', 'onclick', 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' . Mage::getUrl('*/*/delete', array(
                $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'ret'           => 'pending',
            )) .'\')' );
            Mage::register('ret', 'pending');
        }

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $reviewData = Mage::getModel('review/review')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('review_data', $reviewData);
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('review_data') && Mage::registry('review_data')->getId() ) {
            return __('Edit Review') . " '" . Mage::registry('review_data')->getTitle() . "'";
        } else {
            return __('New Review');
        }
    }
}