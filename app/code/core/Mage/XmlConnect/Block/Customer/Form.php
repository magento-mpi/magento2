<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $editFlag = (int)$this->getRequest()->getParam('edit');
        $customer  = $this->getCustomer();
        /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
        $xmlModel  = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<node></node>'));
        //Enterprise_Customer
        if ($editFlag == 1 && $customer && $customer->getId()) {
            $firstname = $xmlModel->escapeXml($customer->getFirstname());
            $lastname  = $xmlModel->escapeXml($customer->getLastname());
            $email     = $xmlModel->escapeXml($customer->getEmail());
        } else {
            $firstname = $lastname = $email = '';
        }

        if ($editFlag) {
            $passwordManageXml = '
                   <field name="change_password" type="checkbox" label="' . $xmlModel->escapeXml($this->__('Change Password')) . '"/>
                </fieldset>
                <fieldset>
                    <field name="current_password" type="password" label="' . $xmlModel->escapeXml($this->__('Current Password')) . '"/>
                    <field name="password" type="password" label="' . $xmlModel->escapeXml($this->__('New Password')) . '"/>
                    <field name="confirmation" type="password" label="' . $xmlModel->escapeXml($this->__('Confirm New Password')) . '">
                        <validators>
                            <validator type="confirmation" message="' . $xmlModel->escapeXml($this->__('Regular and confirmation passwords must be equal')) . '">password</validator>
                        </validators>
                    </field>
                </fieldset>';
        } else {
            $passwordManageXml = '
                    <field name="password" type="password" label="' . $xmlModel->escapeXml($this->__('Password')) . '" required="true"/>
                    <field name="confirmation" type="password" label="' . $xmlModel->escapeXml($this->__('Confirm Password')) . '" required="true">
                        <validators>
                            <validator type="confirmation" message="' . $xmlModel->escapeXml($this->__('Regular and confirmation passwords must be equal')) . '">password</validator>
                        </validators>
                    </field>
                </fieldset>';
        }

        $xml = <<<EOT
<form name="account_form" method="post">
    <fieldset>
        <field name="firstname" type="text" label="{$xmlModel->escapeXml($this->__('First Name'))}" required="true" value="$firstname" />
        <field name="lastname" type="text" label="{$xmlModel->escapeXml($this->__('Last Name'))}" required="true" value="$lastname" />
        <field name="email" type="text" label="{$xmlModel->escapeXml($this->__('Email'))}" required="true" value="$email">
            <validators>
                <validator type="email" message="{$xmlModel->escapeXml($this->__('Wrong email format'))}"/>
            </validators>
        </field>
        $passwordManageXml
</form>
EOT;

        return $xml;
    }
}
