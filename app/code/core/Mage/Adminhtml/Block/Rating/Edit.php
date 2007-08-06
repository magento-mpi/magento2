<?php
/**
 * Rating edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Rating_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_controller = 'rating';

        $this->_updateButton('save', 'label', __('Save Rating'));
        $this->_updateButton('delete', 'label', __('Delete Rating'));

        if( $this->getRequest()->getParam($this->_objectId) ) {

            $ratingData = Mage::getModel('rating/rating')
                ->load($this->getRequest()->getParam($this->_objectId));

            Mage::register('rating_data', $ratingData);
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('rating_data') && Mage::registry('rating_data')->getId() ) {
            return __('Edit Rating') . " '" . Mage::registry('rating_data')->getRatingCode() . "'";
        } else {
            return __('New Rating');
        }
    }
}