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
 * Coupons generation parameters form
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Coupons;

class Form
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Sales rule coupon
     *
     * @var \Magento\SalesRule\Helper\Coupon
     */
    protected $_salesRuleCoupon = null;

    /**
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\SalesRule\Helper\Coupon $salesRuleCoupon
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Factory $formFactory,
        \Magento\SalesRule\Helper\Coupon $salesRuleCoupon,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_salesRuleCoupon = $salesRuleCoupon;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare coupon codes generation parameters form
     *
     * @return \Magento\Adminhtml\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        /**
         * @var \Magento\SalesRule\Helper\Coupon $couponHelper
         */
        $couponHelper = $this->_salesRuleCoupon;

        $model = $this->_coreRegistry->registry('current_promo_quote_rule');
        $ruleId = $model->getId();

        $form->setHtmlIdPrefix('coupons_');

        $gridBlock = $this->getLayout()->getBlock('promo_quote_edit_tab_coupons_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset = $form->addFieldset('information_fieldset', array('legend'=>__('Coupons Information')));
        $fieldset->addClass('ignore-validate');

        $fieldset->addField('rule_id', 'hidden', array(
            'name'     => 'rule_id',
            'value'    => $ruleId
        ));

        $fieldset->addField('qty', 'text', array(
            'name'     => 'qty',
            'label'    => __('Coupon Qty'),
            'title'    => __('Coupon Qty'),
            'required' => true,
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset->addField('length', 'text', array(
            'name'     => 'length',
            'label'    => __('Code Length'),
            'title'    => __('Code Length'),
            'required' => true,
            'note'     => __('Excluding prefix, suffix and separators.'),
            'value'    => $couponHelper->getDefaultLength(),
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset->addField('format', 'select', array(
            'label'    => __('Code Format'),
            'name'     => 'format',
            'options'  => $couponHelper->getFormatsList(),
            'required' => true,
            'value'    => $couponHelper->getDefaultFormat()
        ));

        $fieldset->addField('prefix', 'text', array(
            'name'  => 'prefix',
            'label' => __('Code Prefix'),
            'title' => __('Code Prefix'),
            'value' => $couponHelper->getDefaultPrefix()
        ));

        $fieldset->addField('suffix', 'text', array(
            'name'  => 'suffix',
            'label' => __('Code Suffix'),
            'title' => __('Code Suffix'),
            'value' => $couponHelper->getDefaultSuffix()
        ));

        $fieldset->addField('dash', 'text', array(
            'name'  => 'dash',
            'label' => __('Dash Every X Characters'),
            'title' => __('Dash Every X Characters'),
            'note'  => __('If empty no separation.'),
            'value' => $couponHelper->getDefaultDashInterval(),
            'class' => 'validate-digits'
        ));

        $idPrefix = $form->getHtmlIdPrefix();
        $generateUrl = $this->getGenerateUrl();

        $fieldset->addField('generate_button', 'note', array(
            'text' => $this->getButtonHtml(
                __('Generate'),
                "generateCouponCodes('{$idPrefix}' ,'{$generateUrl}', '{$gridBlockJsObject}')",
                'generate'
            )
        ));

        $this->setForm($form);

        $this->_eventManager->dispatch('adminhtml_promo_quote_edit_tab_coupons_form_prepare_form', array(
            'form' => $form,
        ));

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('adminhtml/*/generate');
    }
}
