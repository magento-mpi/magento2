<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info extends Magento_Adminhtml_Block_Widget_Form
{

    protected $_template = 'edit/tab/info.phtml';

    public function __construct(Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    /**
     * Init form fields
     *
     * @return Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info
     */
    public function initForm()
    {
        $form = new Magento_Data_Form();
        $form->setHtmlIdPrefix('_info');

        $model = Mage::registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>__('Information'))
        );

        if ($model->getId()){
            $fieldset->addField('code', 'label', array(
                'name'      => 'code',
                'label'     => __('Gift Card Code'),
                'title'     => __('Gift Card Code')
            ));

            $fieldset->addField('state_text', 'label', array(
                'name'      => 'state_text',
                'label'     => __('Status'),
                'title'     => __('Status')
            ));
        }

        $fieldset->addField('status', 'select', array(
            'label'     => __('Active'),
            'title'     => __('Active'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                    __('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                    __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('status', Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED);
        }

        $fieldset->addField('is_redeemable', 'select', array(
            'label'     => __('Redeemable'),
            'title'     => __('Redeemable'),
            'name'      => 'is_redeemable',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE =>
                    __('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::NOT_REDEEMABLE =>
                    __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_redeemable', Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE);
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('website_id', 'select', array(
                'name'      => 'website_id',
                'label'     => __('Website'),
                'title'     => __('Website'),
                'required'  => true,
                'values'    => Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteValuesForForm(true),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addType('price', 'Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Form_Price');

        $note = '';
        if (Mage::app()->isSingleStoreMode()) {
            $currencies = $this->_getCurrency();
            $note = '<b>[' . array_shift($currencies) . ']</b>';
        }
        $fieldset->addField('balance', 'price', array(
            'label'     => __('Balance'),
            'title'     => __('Balance'),
            'name'      => 'balance',
            'class'     => 'validate-number',
            'required'  => true,
            'note'      => '<div id="balance_currency">' . $note . '</div>',
        ));

        $fieldset->addField('date_expires', 'date', array(
            'name'   => 'date_expires',
            'label'  => __('Expiration Date'),
            'title'  => __('Expiration Date'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT)
        ));

        $form->setValues($model->getData());

        $this->setForm($form);
        return $this;
    }

    /**
     * Get array of base currency codes among all existing web sites
     *
     * @return array
     */
    protected function _getCurrency()
    {
        $result = array();
        $websites = Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteCollection();
        foreach ($websites as $id => $website) {
            $result[$id] = $website->getBaseCurrencyCode();
        }
        return $result;
    }

    /**
     * Encode currency array to Json string
     *
     * @return string
     */
    public function getCurrencyJson()
    {
        $result = $this->_getCurrency();
        return $this->_coreData->jsonEncode($result);
    }
}
