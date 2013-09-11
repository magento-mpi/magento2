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
 * Coupon codes grid
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Coupons;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('couponCodesGrid');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $priceRule = \Mage::registry('current_promo_quote_rule');

        /**
         * @var \Magento\SalesRule\Model\Resource\Coupon\Collection $collection
         */
        $collection = \Mage::getResourceModel('Magento\SalesRule\Model\Resource\Coupon\Collection')
            ->addRuleToFilter($priceRule)
            ->addGeneratedCouponsFilter();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header' => __('Coupon Code'),
            'index'  => 'code'
        ));

        $this->addColumn('created_at', array(
            'header' => __('Created'),
            'index'  => 'created_at',
            'type'   => 'datetime',
            'align'  => 'center',
            'width'  => '160'
        ));

        $this->addColumn('used', array(
            'header'   => __('Uses'),
            'index'    => 'times_used',
            'width'    => '100',
            'type'     => 'options',
            'options'  => array(
                __('No'),
                __('Yes')
            ),
            'renderer' => '\Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Coupons\Grid\Column\Renderer\Used',
            'filter_condition_callback' => array(
                \Mage::getResourceModel('Magento\SalesRule\Model\Resource\Coupon\Collection'), 'addIsUsedFilterCallback'
            )
        ));

        $this->addColumn('times_used', array(
            'header' => __('Times Used'),
            'index'  => 'times_used',
            'width'  => '50',
            'type'   => 'number',
        ));

        $this->addExportType('*/*/exportCouponsCsv', __('CSV'));
        $this->addExportType('*/*/exportCouponsXml', __('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * Configure grid mass actions
     *
     * @return \Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Coupons\Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('coupon_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseAjax(true);
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> __('Delete'),
             'url'  => $this->getUrl('*/*/couponsMassDelete', array('_current' => true)),
             'confirm' => __('Are you sure you want to delete the selected coupon(s)?'),
             'complete' => 'refreshCouponCodesGrid'
        ));

        return $this;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/couponsGrid', array('_current'=> true));
    }
}
