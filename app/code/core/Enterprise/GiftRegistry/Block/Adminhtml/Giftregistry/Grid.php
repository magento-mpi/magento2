<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set default sort
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('giftregistryGrid');
        $this->setDefaultSort('type_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Type')->getCollection()
            ->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('type_id', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('ID'),
            'align'  => 'right',
            'width'  => 50,
            'index'  => 'type_id'
        ));

        $this->addColumn('code', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Code'),
            'index'  => 'code'
        ));


        $this->addColumn('label', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Label'),
            'index'  => 'label'
        ));

        $this->addColumn('sort_order', array(
            'header' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Sort Order'),
            'index'  => 'sort_order',
            'default' => '-'
        ));

        $this->addColumn('is_listed', array(
            'header'  => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Is Listed'),
            'index'   => 'is_listed',
            'type'    => 'options',
            'options' => array(
                '0' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('No'),
                '1' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Yes')
            )
        ));
        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'    => $row->getId()
        ));
    }
}
