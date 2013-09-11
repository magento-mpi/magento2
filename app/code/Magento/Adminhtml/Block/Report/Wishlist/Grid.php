<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml wishlist report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Report\Wishlist;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('wishlistReportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {

        $collection = \Mage::getResourceModel('Magento\Reports\Model\Resource\Wishlist\Product\Collection')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addWishlistCount();

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    =>__('Name'),
            'index'     =>'name'
        ));

        $this->addColumn('wishlists', array(
            'header'    =>__('Wish Lists'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'wishlists'
        ));

        $this->addColumn('bought_from_wishlists', array(
            'header'    =>__('Wishlist Purchase'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'bought_from_wishlists'
        ));

        $this->addColumn('w_vs_order', array(
            'header'    =>__('Wish List vs. Regular Order'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'w_vs_order'
        ));

        $this->addColumn('num_deleted', array(
            'header'    =>__('Times Deleted'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'num_deleted'
        ));

        $this->addExportType('*/*/exportWishlistCsv', __('CSV'));
        $this->addExportType('*/*/exportWishlistExcel', __('Excel XML'));

        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }

}

