<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Social_Facebook_Block_Adminhtml_Facebuttons extends Mage_Backend_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_selectRenderer;

    /**
     * Retrieve checkbox column renderer
     *
     * @return Social_Facebook_Block_Adminhtml_Select
     */
    protected function _selectRenderer()
    {
        if (!$this->_selectRenderer) {
            $this->_selectRenderer = $this->getLayout()->createBlock(
                'Social_Facebook_Block_Adminhtml_Select', $this->getNameInLayout() . '_customer_group_select',
                array('data' => array('is_render_to_js_template' => true))
            );
            $this->_selectRenderer->setClass('customer_group_select');
            $this->_selectRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_selectRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('action', array(
            'label' => Mage::helper('Social_Facebook_Helper_Data')->__('Action'),
            'style' => 'width:120px',
        ));
        $this->addColumn('title', array(
            'label' => Mage::helper('Social_Facebook_Helper_Data')->__('Button Title'),
            'style' => 'width:120px',
        ));
        $this->addColumn('box', array(
            'label'     => Mage::helper('Social_Facebook_Helper_Data')->__('Enable FriendBox'),
            'renderer'  => $this->_selectRenderer(),
        ));
        $this->addColumn('count', array(
            'label' => Mage::helper('Social_Facebook_Helper_Data')->__('Count in FriendBox'),
            'style' => 'width:120px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('Social_Facebook_Helper_Data')->__('Add Action Button');
    }

    /**
     * Prepare existing row data object
     *
     * @param Magento_Object
     */
    protected function _prepareArrayRow(Magento_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_selectRenderer()->calcOptionHash($row->getData('box')),
            'selected="selected"'
        );
    }

}
