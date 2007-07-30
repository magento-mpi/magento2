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

class Mage_Adminhtml_Block_Widget_Grid_Container extends Mage_Core_Block_Template
{

    protected $_block = 'empty';
    protected $_addButtonLabel = 'Add New';
    protected $_headerText = 'Grid Container Widget';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/grid/container.phtml');
        $this->_init();
    }

    protected function _init()
    {
        return $this;
    }


    protected function _initChildren()
    {
        $this->setChild('addNewButton', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label'     => $this->_addButtonLabel,
            'onclick'   => 'location.href=\''.Mage::getUrl('adminhtml/' . $this->_block . '/new').'\'',
            'class'     => 'add',
        )));
        $this->setChild( 'grid', $this->getLayout()->createBlock( 'adminhtml/' . $this->_block . '_grid', $this->_block . '.grid' ) );
        return $this;
    }

    public function getCreateUrl()
    {
    	return $this->getUrl('*/*/new');
    }

    public function getHeaderText()
    {
    	return $this->_headerText;
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('addNewButton');
    }

    public function getHeaderCssClass()
    {
        return 'head-' . strtr($this->_block, '_', '-');
    }

}

