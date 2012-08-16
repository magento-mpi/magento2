<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping Grid
 *
 * @category   Enterprise
 * @package    Enterprise_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('giftwrappingGrid');
        $this->setDefaultSort('wrapping_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare related item collection
     *
     * @return Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping')->getCollection()
            ->addStoreAttributesToResult()
            ->addWebsitesToResult();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('wrapping_id', array(
            'header' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('ID'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'wrapping_id'
        ));

        $this->addColumn('design', array(
            'header' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Gift Wrapping Design'),
            'index'  => 'design'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header'    => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Websites'),
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteOptionHash()
            ));
        }

        $statusList = array(
            Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Disabled'),
            Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Enabled')
        );
        $this->addColumn('status', array(
            'header'  => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'width'   => '100px',
            'options' => $statusList
        ));

        $this->addColumn('base_price', array(
            'header'  => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Price'),
            'index'   => 'base_price',
            'type'    => 'price',
            'currency_code' => Mage::app()->getWebsite()->getBaseCurrencyCode()
        ));

        $this->addColumn('action',
            array(
                'header'  => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Action'),
                'width'   => '50px',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Edit'),
                        'url' => array(
                            'base' => '*/*/edit',
                            'params' => array()
                        ),
                        'field' => 'id'
                    )
                ),
                'filter'   => false,
                'sortable' => false
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('wrapping_id');
        $this->getMassactionBlock()->setFormFieldName('wrapping_ids');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Are you sure you want to delete the selected gift wrappings?')
        ));

        $statusList = array(
            array('label' => '', 'value' => ''),
            array('label' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Enabled'), 'value' => '1'),
            array('label' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Disabled'), 'value' => '0')
        );

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Change status'),
            'url'  => $this->getUrl('*/*/changeStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'status',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Status'),
                    'values' => $statusList
                )
            )
        ));

        return $this;
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId()
        ));
    }
}
