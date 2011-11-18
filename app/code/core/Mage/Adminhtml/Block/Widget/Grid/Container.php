<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Container extends Mage_Adminhtml_Block_Widget_Container
{

    protected $_addButtonLabel;
    protected $_backButtonLabel;
    protected $_blockGroup = 'Mage_Adminhtml';

    public function __construct()
    {
        if (is_null($this->_addButtonLabel)) {
            $this->_addButtonLabel = $this->__('Add New');
        }
        if(is_null($this->_backButtonLabel)) {
            $this->_backButtonLabel = $this->__('Back');
        }

        parent::__construct();

        $this->setTemplate('Mage_Adminhtml::widget/grid/container.phtml');

        $this->_addButton('add', array(
            'label'     => $this->getAddButtonLabel(),
            'onclick'   => 'setLocation(\'' . $this->getCreateUrl() .'\')',
            'class'     => 'add',
        ));
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                $this->_blockGroup
                    . '_Block_'
                    . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller)))
                    . '_Grid',
                $this->_controller . '.grid')
                ->setSaveParametersInSession(true)
        );
        return parent::_prepareLayout();
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

    protected function getBackButtonLabel()
    {
        return $this->_backButtonLabel;
    }

    protected function _addBackButton()
    {
        $this->_addButton('back', array(
            'label'     => $this->getBackButtonLabel(),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'     => 'back',
        ));
    }

    public function getHeaderCssClass()
    {
        return 'icon-head ' . parent::getHeaderCssClass();
    }

    public function getHeaderWidth()
    {
        return 'width:50%;';
    }
}
