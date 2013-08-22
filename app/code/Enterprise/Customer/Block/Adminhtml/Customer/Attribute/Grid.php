<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Attributes Grid Block
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid
    extends Magento_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Initialize grid, set grid Id
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerAttributeGrid');
        $this->setDefaultSort('sort_order');
    }

    /**
     * Prepare customer attributes grid collection object
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Customer_Model_Resource_Attribute_Collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare customer attributes grid columns
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('is_visible', array(
            'header'    => __('Visible to Customer'),
            'sortable'  => true,
            'index'     => 'is_visible',
            'type'      => 'options',
            'options'   => array(
                '0' => __('No'),
                '1' => __('Yes'),
            ),
            'align'     => 'center',
        ));

        $this->addColumn('sort_order', array(
            'header'    => __('Sort Order'),
            'sortable'  => true,
            'align'     => 'center',
            'index'     => 'sort_order'
        ));

        return $this;
    }
}
