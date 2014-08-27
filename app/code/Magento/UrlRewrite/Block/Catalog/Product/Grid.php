<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Block\Catalog\Product;

/**
 * Products grid for URL rewrites editing
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Catalog\Block\Adminhtml\Product\Grid
{
    /**
     * Disable massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare columns layout
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => __('ID'),
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );

        $this->addColumn('name', array('header' => __('Name'), 'index' => 'name'));

        $this->addColumn('sku', array('header' => __('SKU'), 'width' => 80, 'index' => 'sku'));
        $this->addColumn(
            'status',
            array(
                'header' => __('Status'),
                'width' => 50,
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_status->getOptionArray()
            )
        );
        return $this;
    }

    /**
     * Get URL for dispatching grid ajax requests
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/productGrid', array('_current' => true));
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/edit', array('product' => $row->getId())) . 'category';
    }
}
