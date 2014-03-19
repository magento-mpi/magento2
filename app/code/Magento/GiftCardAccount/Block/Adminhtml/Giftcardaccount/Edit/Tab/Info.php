<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab;

class Info
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var string
     */
    protected $_template = 'edit/tab/info.phtml';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_systemStore = $systemStore;
    }

    /**
     * Init form fields
     *
     * @return $this
     */
    public function initForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_info');

        $model = $this->_coreRegistry->registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>__('Information'))
        );

        if ($model->getId()) {
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
                \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED => __('Yes'),
                \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_DISABLED => __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('status', \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED);
        }

        $fieldset->addField('is_redeemable', 'select', array(
            'label'     => __('Redeemable'),
            'title'     => __('Redeemable'),
            'name'      => 'is_redeemable',
            'required'  => true,
            'options'   => array(
                \Magento\GiftCardAccount\Model\Giftcardaccount::REDEEMABLE => __('Yes'),
                \Magento\GiftCardAccount\Model\Giftcardaccount::NOT_REDEEMABLE => __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_redeemable', \Magento\GiftCardAccount\Model\Giftcardaccount::REDEEMABLE);
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('website_id', 'select', array(
                'name'      => 'website_id',
                'label'     => __('Website'),
                'title'     => __('Website'),
                'required'  => true,
                'values'    => $this->_systemStore->getWebsiteValuesForForm(true),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addType('price', 'Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Form\Price');

        $note = '';
        if ($this->_storeManager->isSingleStoreMode()) {
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
            'date_format' => $this->_localeDate->getDateFormat(\Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT)
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
        $websites = $this->_systemStore->getWebsiteCollection();
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
        return $this->_jsonEncoder->encode($result);
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
