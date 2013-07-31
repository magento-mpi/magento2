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
class Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Form
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare coupon codes generation parameters form
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();

        /**
         * @var Mage_SalesRule_Helper_Coupon $couponHelper
         */
        $couponHelper = Mage::helper('Mage_SalesRule_Helper_Coupon');

        $model = Mage::registry('current_promo_quote_rule');
        $ruleId = $model->getId();

        $form->setHtmlIdPrefix('coupons_');

        $gridBlock = $this->getLayout()->getBlock('promo_quote_edit_tab_coupons_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset = $form->addFieldset('information_fieldset', array('legend'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupons Information')));
        $fieldset->addClass('ignore-validate');

        $fieldset->addField('rule_id', 'hidden', array(
            'name'     => 'rule_id',
            'value'    => $ruleId
        ));

        $fieldset->addField('qty', 'text', array(
            'name'     => 'qty',
            'label'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon Qty'),
            'title'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon Qty'),
            'required' => true,
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset->addField('length', 'text', array(
            'name'     => 'length',
            'label'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Code Length'),
            'title'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Code Length'),
            'required' => true,
            'note'     => Mage::helper('Mage_SalesRule_Helper_Data')->__('Excluding prefix, suffix and separators.'),
            'value'    => $couponHelper->getDefaultLength(),
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset->addField('format', 'select', array(
            'label'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Code Format'),
            'name'     => 'format',
            'options'  => $couponHelper->getFormatsList(),
            'required' => true,
            'value'    => $couponHelper->getDefaultFormat()
        ));

        $fieldset->addField('prefix', 'text', array(
            'name'  => 'prefix',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Code Prefix'),
            'title' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Code Prefix'),
            'value' => $couponHelper->getDefaultPrefix()
        ));

        $fieldset->addField('suffix', 'text', array(
            'name'  => 'suffix',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Code Suffix'),
            'title' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Code Suffix'),
            'value' => $couponHelper->getDefaultSuffix()
        ));

        $fieldset->addField('dash', 'text', array(
            'name'  => 'dash',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Dash Every X Characters'),
            'title' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Dash Every X Characters'),
            'note'  => Mage::helper('Mage_SalesRule_Helper_Data')->__('If empty no separation.'),
            'value' => $couponHelper->getDefaultDashInterval(),
            'class' => 'validate-digits'
        ));

        $idPrefix = $form->getHtmlIdPrefix();
        $generateUrl = $this->getGenerateUrl();

        $fieldset->addField('generate_button', 'note', array(
            'text' => $this->getButtonHtml(
                Mage::helper('Mage_SalesRule_Helper_Data')->__('Generate'),
                "generateCouponCodes('{$idPrefix}' ,'{$generateUrl}', '{$gridBlockJsObject}')",
                'generate'
            )
        ));

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_promo_quote_edit_tab_coupons_form_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('*/*/generate');
    }
}
