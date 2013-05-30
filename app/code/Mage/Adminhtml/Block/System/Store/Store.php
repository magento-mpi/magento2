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
 * Adminhtml store content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Store_Store extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Mage_Adminhtml';

    protected function _construct()
    {
        $this->_controller  = 'system_store';
        $this->_headerText  = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Manage Stores');
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        /* Update default add button to add website button */
        $this->_updateButton('add', 'label', Mage::helper('Mage_Core_Helper_Data')->__('Create Web Site'));
        $this->_updateButton('add', 'onclick', null);

        /* Add Store Group button */

        /** @var $storeLimitation Mage_Core_Model_Store_Group_Limitation */
        $storeLimitation = Mage::getObjectManager()->get('Mage_Core_Model_Store_Group_Limitation');
        if ($storeLimitation->canCreate()) {
            $this->_addButton('add_group', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Create Store'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/newGroup') .'\')',
                'class'     => 'add',
            ));
        }

        /* Add Store button */

        /** @var $limitation Mage_Core_Model_Store_Limitation */
        $limitation = Mage::getObjectManager()->get('Mage_Core_Model_Store_Limitation');
        if ($limitation->canCreate()) {
            $this->_addButton('add_store', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Create Store View'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/newStore') .'\')',
                'class'     => 'add',
            ));
        }

        return parent::_prepareLayout();
    }

    /**
     * Make additional actions, before rendering the block
     *
     * @return Mage_Adminhtml_Block_System_Store_Store
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        // Add javascript for the Create Website button
        /** @var $block Mage_Adminhtml_Block_System_Store_Button_CreateWebsiteJs */
        $block = $this->_layout->createBlock('Mage_Adminhtml_Block_System_Store_Button_CreateWebsiteJs');
        $block->setHtmlId('add');
        $html = $block->toHtml();
        $this->_updateButton('add', 'after_html', $html);

        return $this;
    }
}
