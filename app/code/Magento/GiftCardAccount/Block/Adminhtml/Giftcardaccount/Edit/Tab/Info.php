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

class Info extends \Magento\Adminhtml\Block\Widget\Form
{

    protected $_template = 'edit/tab/info.phtml';

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Init form fields
     *
     * @return \Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\Info
     */
    public function initForm()
    {
        $form = new \Magento\Data\Form();
        $form->setHtmlIdPrefix('_info');

        $model = \Mage::registry('current_giftcardaccount');

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
                \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED =>
                    __('Yes'),
                \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_DISABLED =>
                    __('No'),
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
                \Magento\GiftCardAccount\Model\Giftcardaccount::REDEEMABLE =>
                    __('Yes'),
                \Magento\GiftCardAccount\Model\Giftcardaccount::NOT_REDEEMABLE =>
                    __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_redeemable', \Magento\GiftCardAccount\Model\Giftcardaccount::REDEEMABLE);
        }

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('website_id', 'select', array(
                'name'      => 'website_id',
                'label'     => __('Website'),
                'title'     => __('Website'),
                'required'  => true,
                'values'    => \Mage::getSingleton('Magento\Core\Model\System\Store')->getWebsiteValuesForForm(true),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addType('price', 'Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Form\Price');

        $note = '';
        if (\Mage::app()->isSingleStoreMode()) {
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
            'date_format' => \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT)
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
        $websites = \Mage::getSingleton('Magento\Core\Model\System\Store')->getWebsiteCollection();
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
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result);
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
