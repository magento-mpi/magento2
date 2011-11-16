<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('edit/tab/info.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_info');

        $model = Mage::registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Information'))
        );

        if ($model->getId()){
            $fieldset->addField('code', 'label', array(
                'name'      => 'code',
                'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card Code'),
                'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card Code')
            ));

            $fieldset->addField('state_text', 'label', array(
                'name'      => 'state_text',
                'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Status'),
                'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Status')
            ));
        }

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Active'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Active'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('status', Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED);
        }

        $fieldset->addField('is_redeemable', 'select', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Redeemable'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Redeemable'),
            'name'      => 'is_redeemable',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::NOT_REDEEMABLE =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_redeemable', Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE);
        }

        $fieldset->addField('website_id', 'select', array(
            'name'      => 'website_id',
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Website'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Website'),
            'required'  => true,
            'values'    => Mage::getSingleton('Mage_Adminhtml_Model_System_Store')->getWebsiteValuesForForm(true),
        ));

        $fieldset->addType('price', 'Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Form_Price');

        $fieldset->addField('balance', 'price', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Balance'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Balance'),
            'name'      => 'balance',
            'class'     => 'validate-number',
            'required'  => true,
            'note'      => '<div id="balance_currency"></div>'
        ));

        $fieldset->addField('date_expires', 'date', array(
            'name'   => 'date_expires',
            'label'  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Expiration Date'),
            'title'  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Expiration Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));

        $form->setValues($model->getData());

        $this->setForm($form);
        return $this;
    }

    public function getCurrencyJson()
    {
        $result = array();
        $websites = Mage::getSingleton('Mage_Adminhtml_Model_System_Store')->getWebsiteCollection();
        foreach ($websites as $id=>$website) {
            $result[$id] = $website->getBaseCurrencyCode();
        }

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }
}
