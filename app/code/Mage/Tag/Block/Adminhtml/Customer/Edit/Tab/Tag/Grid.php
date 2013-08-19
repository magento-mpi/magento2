<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer's tags grid
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Customer_Model_Customer|int getCustomerId() getCustomerId()
 * @method Mage_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag_Grid setCustomerId() setCustomerId(int $customerId)
 * @method Mage_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag_Grid setUseAjax() setUseAjax(boolean $useAjax)
 */
class Mage_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag_Grid extends Mage_Backend_Block_Widget_Grid_Extended
{
    /**
     * Initialize grid parameters
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare data collection for output
     *
     * @return Mage_Tag_Model_Resource_Customer_Collection
     */
    protected function _prepareCollection()
    {
        if ($this->getCustomerId() instanceof Mage_Customer_Model_Customer) {
            $this->setCustomerId($this->getCustomerId()->getId());
        }

        /** @var $collection Mage_Tag_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('Mage_Tag_Model_Resource_Customer_Collection');
        $collection->addCustomerFilter($this->getCustomerId())
            ->addGroupByTag();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Manual adding of product name
     *
     * @return Mage_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag_Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $collection Mage_Tag_Model_Resource_Customer_Collection */
        $collection = $this->getCollection();
        $collection->addProductName();
        return parent::_afterLoadCollection();
    }

    /**
     * Add grid columns
     *
     * @return Mage_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => __('Tag'),
            'index'  => 'name',
        ));

        $this->addColumn('status', array(
            'header'  => __('Status'),
            'width'   => '90px',
            'index'   => 'status',
            'type'    => 'options',
            'options' => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => __('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => __('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => __('Approved'),
            ),
            'filter'  => false,
        ));

        $this->addColumn('product', array(
            'header'   => __('Product'),
            'index'    => 'product',
            'filter'   => false,
            'sortable' => false,
        ));

        $this->addColumn('product_sku', array(
            'header'   => __('SKU'),
            'index'    => 'product_sku',
            'filter'   => false,
            'sortable' => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Returns URL for editing of row tag
     *
     * @param Magento_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/tag/edit', array(
            'tag_id'      => $row->getTagId(),
            'customer_id' => $this->getCustomerId(),
        ));
    }

    /**
     * Returns URL for grid updating
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/customer/tagGrid', array(
            '_current' => true,
            'id'       => $this->getCustomerId()
        ));
    }

}
