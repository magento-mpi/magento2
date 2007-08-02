<?php
/**
 * Adminhtml grid container block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Container extends Mage_Adminhtml_Block_Widget_Container
{

    protected $_addButtonLabel = 'Add New';

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('widget/grid/container.phtml');

        $this->_addButton('add', array(
            'label'     => $this->getAddButtonLabel(),
            'onclick'   => 'location.href=\''.Mage::getUrl('adminhtml/' . $this->_controller . '/new').'\'',
            'class'     => 'add',
        ));
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild( 'grid', $this->getLayout()->createBlock( 'adminhtml/' . $this->_controller . '_grid', $this->_controller . '.grid' ) );
        return $this;
    }

    public function getCreateUrl()
    {
    	return $this->getUrl('*/*/new');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    protected function getAddButtonLabel()
    {
        return $this->_addButtonLabel;
    }

}

