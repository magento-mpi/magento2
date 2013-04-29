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
 * Coupon codes grid
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $priceRule = Mage::registry('current_promo_quote_rule');

        /**
         * @var Mage_SalesRule_Model_Resource_Coupon_Collection $collection
         */
        $collection = Mage::getResourceModel('Mage_SalesRule_Model_Resource_Coupon_Collection')
            ->addRuleToFilter($priceRule)
            ->addGeneratedCouponsFilter();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon Code'),
            'index'  => 'code'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Created'),
            'index'  => 'created_at',
            'type'   => 'datetime',
            'align'  => 'center',
            'width'  => '160'
        ));

        $this->addColumn('used', array(
            'header'   => Mage::helper('Mage_SalesRule_Helper_Data')->__('Used'),
            'index'    => 'times_used',
            'width'    => '100',
            'type'     => 'options',
            'options'  => array(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('No'),
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes')
            ),
            'renderer' => 'Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid_Column_Renderer_Used',
            'filter_condition_callback' => array(
                Mage::getResourceModel('Mage_SalesRule_Model_Resource_Coupon_Collection'), 'addIsUsedFilterCallback'
            )
        ));

        $this->addColumn('times_used', array(
            'header' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Times Used'),
            'index'  => 'times_used',
            'width'  => '50',
            'type'   => 'number',
        ));

        $this->addExportType('*/*/exportCouponsCsv', Mage::helper('Mage_Customer_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportCouponsXml', Mage::helper('Mage_Customer_Helper_Data')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * Configure grid mass actions
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('coupon_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseAjax(true);
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete'),
             'url'  => $this->getUrl('*/*/couponsMassDelete', array('_current' => true)),
             'confirm' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Are you sure you want to delete the selected coupon(s)?'),
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
