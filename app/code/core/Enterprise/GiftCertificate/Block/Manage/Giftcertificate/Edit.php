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

class Enterprise_GiftCertificate_Block_Manage_Giftcertificate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'manage_giftcertificate';
        $this->_blockGroup = 'giftcertificate';

        if ($this->getGiftCertificateId()) {
            $this->_addButton('order', array(
                'label' => Mage::helper('customer')->__('Redeem Gift Certificate'),
                'onclick' => 'setLocation(\'' . $this->getRedeemGiftCertificateUrl() . '\')',
                'class' => 'add',
            ), -1);
        }

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('giftcertificate')->__('Save Gift Certificate'));
        $this->_updateButton('delete', 'label', Mage::helper('giftcertificate')->__('Delete Gift Certificate'));

    }

    public function getRedeemGiftCertificateUrl()
    {
        return $this->getUrl('*/*/redeem', array('giftcertificate_id' => $this->getGiftCertificateId()));
    }

    public function getGiftCertificateId()
    {
        return Mage::registry('current_giftcertificate')->getId();
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_giftcertificate')->getId()) {
            return $this->htmlEscape(Mage::registry('current_giftcertificate')->getCode());
        }
        else {
            return Mage::helper('giftcertificate')->__('New Gift Certificate');
        }
    }

}