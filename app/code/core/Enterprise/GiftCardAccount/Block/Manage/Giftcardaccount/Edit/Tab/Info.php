<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCardAccount_Block_Manage_Giftcardaccount_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_info');
        $form->setFieldNameSuffix('info');

        $model = Mage::registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('enterprise_giftcardaccount')->__('Information'))
        );

        if ($model->getGiftcardaccountId()) {
            $fieldset->addField('giftcardaccount_id', 'hidden', array(
                'name' => 'giftcardaccount_id',
            ));
        }

        if ($model->getId()){
            $fieldset->addField('code', 'label', array(
                'name'      => 'code',
                'label'     => Mage::helper('enterprise_giftcardaccount')->__('Code'),
                'title'     => Mage::helper('enterprise_giftcardaccount')->__('Code'),
            ));

            $fieldset->addField('state', 'label', array(
                'name'      => 'state',
                'label'     => Mage::helper('enterprise_giftcardaccount')->__('State'),
                'title'     => Mage::helper('enterprise_giftcardaccount')->__('State'),
                'value'     => $model->getStateText(),
            ));
        }

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('enterprise_giftcardaccount')->__('Is Active'),
            'title'     => Mage::helper('enterprise_giftcardaccount')->__('Is Active'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                    Mage::helper('enterprise_giftcardaccount')->__('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                    Mage::helper('enterprise_giftcardaccount')->__('No'),
            ),
        ));

        $fieldset->addField('is_redeemable', 'select', array(
            'label'     => Mage::helper('enterprise_giftcardaccount')->__('Is Redeemable'),
            'title'     => Mage::helper('enterprise_giftcardaccount')->__('Is Redeemable'),
            'name'      => 'is_redeemable',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE =>
                    Mage::helper('enterprise_giftcardaccount')->__('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::NOT_REDEEMABLE =>
                    Mage::helper('enterprise_giftcardaccount')->__('No'),
            ),
        ));

        $fieldset->addField('website_id', 'select', array(
            'name'      => 'website_id',
            'label'     => Mage::helper('enterprise_giftcardaccount')->__('Website'),
            'title'     => Mage::helper('enterprise_giftcardaccount')->__('Website'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true, true),
        ));


        $fieldset->addField('balance', 'text', array(
            'label'     => Mage::helper('enterprise_giftcardaccount')->__('Balance'),
            'title'     => Mage::helper('enterprise_giftcardaccount')->__('Balance'),
            'name'      => 'balance',
            'class'     => 'validate-number',
            'required'  => true,
        ));

    	$fieldset->addField('date_expires', 'date', array(
            'name'   => 'date_expires',
            'label'  => Mage::helper('enterprise_giftcardaccount')->__('Expiration Date'),
            'title'  => Mage::helper('enterprise_giftcardaccount')->__('Expiration Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));

        $form->setValues($model->getData());
        if ($model->getId()) {
            $form->addValues(array('state'=>$model->getStateText()));
        }
        $this->setForm($form);
        return $this;
    }
}