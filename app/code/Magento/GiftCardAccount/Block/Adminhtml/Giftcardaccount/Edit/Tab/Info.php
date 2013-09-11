<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info extends Magento_Adminhtml_Block_Widget_Form
{

    protected $_template = 'edit/tab/info.phtml';

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $formFactory, $data);
    }

    /**
     * Init form fields
     *
     * @return Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info
     */
    public function initForm()
    {
        $form = $this->_createForm();
        $form->setHtmlIdPrefix('_info');

        $model = $this->_coreRegistry->registry('current_giftcardaccount');

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
                Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED => __('Yes'),
                Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED => __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('status', Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED);
        }

        $fieldset->addField('is_redeemable', 'select', array(
            'label'     => __('Redeemable'),
            'title'     => __('Redeemable'),
            'name'      => 'is_redeemable',
            'required'  => true,
            'options'   => array(
                Magento_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE => __('Yes'),
                Magento_GiftCardAccount_Model_Giftcardaccount::NOT_REDEEMABLE => __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_redeemable', Magento_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE);
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

        $fieldset->addType('price', 'Magento_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Form_Price');

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
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($result);
    }

    /**
     * Get is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
