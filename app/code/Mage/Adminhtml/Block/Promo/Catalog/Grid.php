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
 * Catalog Rules Grid
 *
 * @category Mage
 * @package Mage_Adminhtml
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Catalog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     * Set sort settings
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('promo_catalog_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Add websites to catalog rules collection
     * Set collection
     *
     * @return Mage_Adminhtml_Block_Promo_Catalog_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_CatalogRule_Model_Resource_Rule_Collection */
        $collection = Mage::getModel('Mage_CatalogRule_Model_Rule')
            ->getResourceCollection();
        $collection->addWebsitesToResult();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return Mage_Adminhtml_Block_Promo_Catalog_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Rule'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Start on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('End on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Active'),
                0 => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Inactive')
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('rule_website', array(
                'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }

}
