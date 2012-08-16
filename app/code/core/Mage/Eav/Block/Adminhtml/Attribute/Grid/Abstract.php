<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attributes grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('attributeGrid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare default grid column
     *
     * @return Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('attribute_code', array(
            'header'=>Mage::helper('Mage_Eav_Helper_Data')->__('Attribute Code'),
            'sortable'=>true,
            'index'=>'attribute_code'
        ));

        $this->addColumn('frontend_label', array(
            'header'=>Mage::helper('Mage_Eav_Helper_Data')->__('Attribute Label'),
            'sortable'=>true,
            'index'=>'frontend_label'
        ));

        $this->addColumn('is_required', array(
            'header'=>Mage::helper('Mage_Eav_Helper_Data')->__('Required'),
            'sortable'=>true,
            'index'=>'is_required',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('Mage_Eav_Helper_Data')->__('Yes'),
                '0' => Mage::helper('Mage_Eav_Helper_Data')->__('No'),
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_user_defined', array(
            'header'=>Mage::helper('Mage_Eav_Helper_Data')->__('System'),
            'sortable'=>true,
            'index'=>'is_user_defined',
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                '0' => Mage::helper('Mage_Eav_Helper_Data')->__('Yes'),   // intended reverted use
                '1' => Mage::helper('Mage_Eav_Helper_Data')->__('No'),    // intended reverted use
            ),
        ));

        return $this;
    }

    /**
     * Return url of given row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('attribute_id' => $row->getAttributeId()));
    }

}
