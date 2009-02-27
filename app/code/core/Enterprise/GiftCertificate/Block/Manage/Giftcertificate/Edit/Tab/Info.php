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
 * @package    Enterprise_GiftCertificate
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCertificate_Block_Manage_Giftcertificate_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
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

        $model = Mage::registry('current_giftcertificate');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('giftcertificate')->__('Information'))
        );

        if ($model->getGiftcertificateId()) {
            $fieldset->addField('giftcertificate_id', 'hidden', array(
                'name' => 'giftcertificate_id',
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => Mage::helper('giftcertificate')->__('Code'),
            'title'     => Mage::helper('giftcertificate')->__('Code'),
            'required'  => true,
        ));

        $fieldset->addField('website_id', 'select', array(
            'name'      => 'website_id',
            'label'     => Mage::helper('giftcertificate')->__('Website'),
            'title'     => Mage::helper('giftcertificate')->__('Website'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true, true),
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('giftcertificate')->__('Status'),
            'title'     => Mage::helper('giftcertificate')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('giftcertificate')->__('Enabled'),
                '0' => Mage::helper('giftcertificate')->__('Disabled'),
            ),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return $this;
    }
}