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
namespace Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\SalesRule\Model\Resource\Coupon\CollectionFactory
     */
    protected $_salesRuleCoupon;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\SalesRule\Model\Resource\Coupon\CollectionFactory $salesRuleCoupon
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\SalesRule\Model\Resource\Coupon\CollectionFactory $salesRuleCoupon,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_salesRuleCoupon = $salesRuleCoupon;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

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
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $priceRule = $this->_coreRegistry->registry('current_promo_quote_rule');

        /**
         * @var \Magento\SalesRule\Model\Resource\Coupon\Collection $collection
         */
        $collection = $this->_salesRuleCoupon->create()
            ->addRuleToFilter($priceRule)
            ->addGeneratedCouponsFilter();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
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
            'renderer' => 'Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid\Column\Renderer\Used',
            'filter_condition_callback' => array(
                $this->_salesRuleCoupon->create(), 'addIsUsedFilterCallback'
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
     * @return \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('coupon_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseAjax(true);
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> __('Delete'),
             'url'  => $this->getUrl('sales_rule/*/couponsMassDelete', array('_current' => true)),
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
        return $this->getUrl('sales_rule/*/couponsGrid', array('_current'=> true));
    }
}
