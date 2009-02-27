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

class Enterprise_GiftCertificate_Block_Manage_Giftcertificate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcertificate_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftcertificate')->__('Gift Certificate'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('info', array(
            'label'     => Mage::helper('customer')->__('Information'),
            'content'   => $this->getLayout()->createBlock('giftcertificate/manage_giftcertificate_edit_tab_info')->initForm()->toHtml(),
            'active'    => true
        ));

        if (Mage::registry('current_giftcertificate')->getId()) {
            $this->addTab('history', array(
                'label'     => Mage::helper('giftcertificate')->__('History'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/history', array('_current' => true)),
             ));
        }
        return parent::_beforeToHtml();
    }

}