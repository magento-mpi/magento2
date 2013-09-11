<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Item Attributes Grid Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute;

class Grid
    extends \Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
{
    /**
     * Initialize grid, set grid Id
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rmaItemAttributeGrid');
        $this->setDefaultSort('sort_order');
    }

    /**
     * Prepare customer attributes grid collection object
     *
     * @return Magento_Customer_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('Magento\Rma\Model\Resource\Item\Attribute\Collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare customer attributes grid columns
     *
     * @return Magento_Customer_Block_Adminhtml_Customer_Attribute_Grid
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
            'header_css_class'  => 'col-visible-on-front',
            'column_css_class'  => 'col-visible-on-front'
        ));

        $this->addColumn('sort_order', array(
            'header'    => __('Sort Order'),
            'sortable'  => true,
            'align'     => 'center',
            'index'     => 'sort_order',
            'header_css_class'  => 'col-order',
            'column_css_class'  => 'col-order'
        ));

        return $this;
    }
}
