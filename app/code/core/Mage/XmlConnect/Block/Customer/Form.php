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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer form xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form extends Mage_Core_Block_Template
{
    /**
     * Render customer form xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $helper   = Mage::helper('xmlconnect');
        $editFlag = (int)$this->getRequest()->getParam('edit');
        $customer  = $this->getCustomer();

        if ($editFlag == 1 && $customer && $customer->getId()) {
            $xmlModel  = new Mage_XmlConnect_Model_Simplexml_Element('<node></node>');
            $firstname = $xmlModel->xmlentities(strip_tags($customer->getFirstname()));
            $lastname  = $xmlModel->xmlentities(strip_tags($customer->getLastname()));
            $email     = $xmlModel->xmlentities(strip_tags($customer->getEmail()));
        }
        else {
            $firstname = $lastname = $email = '';
        }

        if ($editFlag) {
            $passwordManageXml = '
                   <field name="change_password" type="checkbox" label="' . $helper->__('Change Password') . '"/>
                </fieldset>
                <fieldset>
                    <field name="current_password" type="password" label="' . $helper->__('Current Password') . '"/>
                    <field name="password" type="password" label="' . $helper->__('New Password') . '"/>
                    <field name="confirmation" type="password" label="' . $helper->__('Confirm New Password') . '">
                        <validators>
                            <validator type="confirmation" message="' . $helper->__('Regular and confirmation passwords must be equal') . '">password</validator>
                        </validators>
                    </field>
                </fieldset>';
        }
        else {
            $passwordManageXml = '
                    <field name="password" type="password" label="' . $helper->__('Password') . '" required="true"/>
                    <field name="confirmation" type="password" label="' . $helper->__('Confirm Password') . '" required="true">
                        <validators>
                            <validator type="confirmation" message="' . $helper->__('Regular and confirmation passwords must be equal') . '">password</validator>
                        </validators>
                    </field>
                </fieldset>';
        }

        $xml = <<<EOT
<form name="account_form" method="post">
    <fieldset>
        <field name="firstname" type="text" label="{$helper->__('First Name')}" required="true" value="$firstname">
            <validators>
                <validator type="regexp" message="{$helper->__('Letters only')}">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="lastname" type="text" label="{$helper->__('Last Name')}" required="true" value="$lastname">
            <validators>
                <validator type="regexp" message="{$helper->__('Letters only')}">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="email" type="text" label="{$helper->__('Email')}" required="true" value="$email">
            <validators>
                <validator type="email" message="{$helper->__('Wrong email format')}"/>
            </validators>
        </field>
        $passwordManageXml
</form>
EOT;

        return $xml;
    }

}
