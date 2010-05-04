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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect customer controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_CustomerController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Get customer session model
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Customer authentification action
     */
    public function loginAction()
    {
        $session = $this->_getSession();
        $request = $this->getRequest();
        if ($session->isLoggedIn()) {
            $this->_message($this->__('You are already logged in.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        if ($request->isPost()) {
            $user = $request->getParam('username');
            $pass = $request->getParam('password');
            try {
                if ($session->login($user, $pass)) {
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $session->getCustomer()->sendNewAccountEmail('confirmed');
                    }
                    $this->_message($this->__('Authentification complete.'), self::MESSAGE_STATUS_SUCCESS);
                }
                else {
                    $this->_message($this->__('Invalid login or password.'), self::MESSAGE_STATUS_ERROR);
                }
            }
            catch (Mage_Core_Exception $e) {
                switch ($e->getCode()) {
                    case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                        // TODO: resend configmation email message with action
                        break;
                    case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                        $message = $e->getMessage();
                        break;
                    default:
                        $message = $e->getMessage();
                }
                $this->_message($message, self::MESSAGE_STATUS_ERROR);
            }
            catch (Exception $e) {
                $this->_message($this->__('Customer authentification problem.'), self::MESSAGE_STATUS_ERROR);
            }
        }
        else {
            $this->_message($this->__('Login and password are required.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Customer logout
     *
     */
    public function logoutAction()
    {
        try {
            if ($this->_getSession()->isLoggedIn()) {
                $this->_getSession()->logout();
                $this->_message($this->__('Logout complete.'), self::MESSAGE_STATUS_SUCCESS);
            }
            else {
                $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message($this->__('Customer logout problem.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Customer registration form
     */
    public function formAction()
    {
        $xml = <<<EOT
<?xml version="1.0"?>
<form name="account_form" method="post">
    <fieldset>
        <field name="firstname" type="text" label="First Name" required="true">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="lastname" type="text" label="Last Name" required="true">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="email" type="text" label="Email" required="true">
            <validators>
                <validator type="email" message="Wrong email format"/>
            </validators>
        </field>
        <field name="password" type="password" label="Password" required="true"/>
        <field name="confirmation" type="password" label="Confirm" required="true">
            <validators>
                <validator type="confirmation" message="....">password</validator>
            </validators>
        </field>
    </fieldset>
    <fieldset legend="Receive Email Notifications">
        <field name="is_subscribed" type="checkbox" label="Promos and News"/>
    </fieldset>
</form>
EOT;
        $this->getResponse()->setBody($xml);
    }

    /**
     * Customer account edit form
     */
    public function editFormAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }

        $customer  = $this->_getSession()->getCustomer();
        $xmlModel  = new Varien_Simplexml_Element('<node></node>');
        $firstname = $xmlModel->xmlentities(strip_tags($customer->getFirstname()));
        $lastname  = $xmlModel->xmlentities(strip_tags($customer->getLastname()));
        $email     = $xmlModel->xmlentities(strip_tags($customer->getEmail()));

        $xml = <<<EOT
<?xml version="1.0"?>
<form name="account_form" method="post">
    <fieldset>
        <field name="firstname" type="text" label="First Name" required="true" value="$firstname">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="lastname" type="text" label="Last Name" required="true" value="$lastname">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="email" type="text" label="Email" required="true" value="$email">
            <validators>
                <validator type="email" message="Wrong email format"/>
            </validators>
        </field>
        <field name="change_password" type="checkbox" label="Change Password"/>
    </fieldset>
    <fieldset>
        <field name="current_password" type="password" label="Current Password"/>
        <field name="password" type="password" label="New Password"/>
        <field name="confirmation" type="password" label="Confirm New Password">
            <validators>
                <validator type="confirmation" message="....">password</validator>
            </validators>
        </field>
    </fieldset>
</form>
EOT;
        $this->getResponse()->setBody($xml);
    }

    /**
     * Change customer password action
     */
    public function editAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer/customer')
                ->setId($this->_getSession()->getCustomerId())
                ->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId());

            $fields = Mage::getConfig()->getFieldset('customer_account');
            $data = $this->_filterPostData($this->getRequest()->getPost());

            foreach ($fields as $code=>$node) {
                if ($node->is('update') && isset($data[$code])) {
                    $customer->setData($code, $data[$code]);
                }
            }

            $errors = $customer->validate();
            if (!is_array($errors)) {
                $errors = array();
            }

            /**
             * we would like to preserver the existing group id
             */
            if ($this->_getSession()->getCustomerGroupId()) {
                $customer->setGroupId($this->_getSession()->getCustomerGroupId());
            }

            if ($this->getRequest()->getParam('change_password')) {
                $currPass = $this->getRequest()->getPost('current_password');
                $newPass  = $this->getRequest()->getPost('password');
                $confPass  = $this->getRequest()->getPost('confirmation');

                if (empty($currPass) || empty($newPass) || empty($confPass)) {
                    $errors[] = $this->__('The password fields cannot be empty.');
                }

                if ($newPass != $confPass) {
                    $errors[] = $this->__('Please make sure your passwords match.');
                }

                $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                if (strpos($oldPass, ':')) {
                    list($_salt, $salt) = explode(':', $oldPass);
                } else {
                    $salt = false;
                }

                if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                    $customer->setPassword($newPass);
                } else {
                    $errors[] = $this->__('Invalid current password.');
                }
            }

            if (!empty($errors)) {
                $message = new Varien_Simplexml_Element('<message></message>');
                $message->addChild('status', self::MESSAGE_STATUS_ERROR);
                $message->addChild('text', implode(' ', $errors));
                $this->getResponse()->setBody($message->asNiceXml());
                return;
            }

            try {
                $customer->save();
                $this->_getSession()->setCustomer($customer);
                $this->_message($this->__('The account information has been saved.'), self::MESSAGE_STATUS_SUCCESS);
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
            }
            catch (Exception $e) {
                $this->_message($this->__('Cannot save the customer.'), self::MESSAGE_STATUS_ERROR);
            }
        }
        else {
            $this->_message($this->__('POST data is not valid.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Save customer account
     */
    public function saveAction()
    {
        $session = $this->_getSession();
        $request = $this->getRequest();
        if ($session->isLoggedIn()) {
            $this->_message($this->__('You are already logged in.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($request->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            $data = $this->_filterPostData($request->getPost());

            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                if ($node->is('create') && isset($data[$code])) {
                    if ($code == 'email') {
                        $data[$code] = trim($data[$code]);
                    }
                    $customer->setData($code, $data[$code]);
                }
            }

            if ($request->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

//            if ($request->getPost('create_address')) {
//                $address = Mage::getModel('customer/address')
//                    ->setData($request->getPost())
//                    ->setIsDefaultBilling($request->getParam('default_billing', false))
//                    ->setIsDefaultShipping($request->getParam('default_shipping', false))
//                    ->setId(null);
//                $customer->addAddress($address);
//
//                $errors = $address->validate();
//                if (!is_array($errors)) {
//                    $errors = array();
//                }
//            }

            try {
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
                        $message = $this->__('Account confirmation is required. Please, check your email for the confirmation link.');
                        $messageXmlObj = new Varien_Simplexml_Element('<message></message>');
                        $messageXmlObj->addChild('status', self::MESSAGE_STATUS_SUCCESS);
                        $messageXmlObj->addChild('text', $message);
                        $messageXmlObj->addChild('confirmation', 1);
                        $this->getResponse()->setBody($messageXmlObj->asNiceXml());
                        return;
                    }
                    else {
                        $session->setCustomerAsLoggedIn($customer);
                        $customer->sendNewAccountEmail('registered');
                        $this->_message($this->__('Register and Authentification complete.'), self::MESSAGE_STATUS_SUCCESS);
                        return;
                    }
                }
                else {
                    if (is_array($errors)) {
                        $message = implode("\n", $errors);
                    }
                    else {
                        $message = $this->__('Invalid customer data.');
                    }
                    $this->_message($message, self::MESSAGE_STATUS_ERROR);
                    return ;
                }
            }
            catch (Mage_Core_Exception $e) {
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $message = $this->__('There is already an account with this email address.');
                    $session->setEscapeMessages(false);
                }
                else {
                    $message = $e->getMessage();
                }
                $this->_message($message, self::MESSAGE_STATUS_ERROR);
            }
            catch (Exception $e) {
                $this->_message($this->__('Cannot save the customer.'), self::MESSAGE_STATUS_ERROR);
            }
        }
    }

    /**
     * Send new password to customer by specified email
     */
    public function forgotPasswordAction()
    {
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_message($this->__('Invalid email address.'), self::MESSAGE_STATUS_ERROR);
                return;
            }
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newPassword = $customer->generatePassword();
                    $customer->changePassword($newPassword, false);
                    $customer->sendPasswordReminderEmail();
                    $this->_message($this->__('A new password has been sent.'), self::MESSAGE_STATUS_SUCCESS);

                    return;
                }
                catch (Mage_Core_Exception $e) {
                    $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
                }
                catch (Exception $e) {
                    $this->_message($this->__('Sending/Changing new password problem.'), self::MESSAGE_STATUS_ERROR);
                }
            }
            else {
                $this->_message($this->__('This email address was not found in our records.'), self::MESSAGE_STATUS_ERROR);
            }
        }
        else {
            $this->_message($this->__('Customer email not specified.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Customer addresses list
     */
    public function addressAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }

        if (count($this->_getSession()->getCustomer()->getAddresses())) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
        else {
            $message = new Varien_Simplexml_Element('<message></message>');
            $message->addChild('status', self::MESSAGE_STATUS_ERROR);
            $message->addChild('is_empty_address_book', 1);
            $this->getResponse()->setBody($message->asNiceXml());
        }
    }

    /**
     * Retrieve regions by country
     *
     * @param string $countryId
     * @return array
     */
    protected function _getRegionOptions($countryId)
    {
        $cacheKey = 'DIRECTORY_REGION_SELECT_STORE'.Mage::app()->getStore()->getId();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        }
        else {
            $collection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($countryId)
                ->load();
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }

    /**
     * Retrieve countries
     *
     * @return array
     */
    protected function _getCountryOptions()
    {
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_'.Mage::app()->getStore()->getCode();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        }
        else {
            $collection = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore();
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }

    /**
     * Customer add address form
     */
    public function addressFormAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }

        $xmlModel   = new Varien_Simplexml_Element('<node></node>');
        $countryId = Mage::getStoreConfig('general/country/default');
        $countries = $this->_getCountryOptions();

        $regions = array();
        $countryOptionsXml = '<values>';
        if (is_array($countries)) {
            foreach ($countries as $key => $data) {
                if ($data['value']) {
                    $regions = $this->_getRegionOptions($data['value']);
                }
                $countryOptionsXml .= '
                <item relation="' . (is_array($regions) && !empty($regions) ? 'region_id' : 'region') . '"' . ($countryId == $data['value'] ? ' selected="1"' : '') . '>
                    <label>' . $xmlModel->xmlentities((string)$data['label']) . '</label>
                    <value>' . $xmlModel->xmlentities($data['value']) . '</value>';
                if (is_array($regions) && !empty($regions)) {
                    $countryOptionsXml .= '<regions>';
                    foreach ($regions as $_key => $_data){
                        $countryOptionsXml .= '<region_item>';
                        $countryOptionsXml .=
                            '<label>' . $xmlModel->xmlentities((string)$_data['label']) . '</label>
                             <value>' . $xmlModel->xmlentities($_data['value']) . '</value>';
                        $countryOptionsXml .= '</region_item>';
                    }
                    $countryOptionsXml .= '</regions>';
                }
                $countryOptionsXml .= '</item>';
            }
        }
        $countryOptionsXml .= '</values>';

        $xml = <<<EOT
<?xml version="1.0"?>
<form name="address_form" method="post">
    <fieldset legend="Contact Information">
        <field name="firstname" type="text" label="First Name" required="true">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="lastname" type="text" label="Last Name" required="true">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="company" type="text" label="Company" />
        <field name="telephone" type="text" label="Telephone" required="true" />
        <field name="fax" type="text" label="Fax" />
    </fieldset>
    <fieldset legend="Address">
        <field name="street[]" type="text" label="Street Address" required="true" />
        <field name="street[]" type="text" />
        <field name="city" type="text" label="City" required="true" />
        <field name="region" type="text" label="State/Province" />
        <field name="region_id" type="select" label="State/Province" required="true" />
        <field name="postcode" type="text" label="Zip/Postal Code" required="true" />
        <field name="country_id" type="select" label="Country" required="true">
            $countryOptionsXml
        </field>
        <field name="default_billing" type="checkbox" label="Use as my default billing address"/>
        <field name="default_shipping" type="checkbox" label="Use as my default shipping address"/>
    </fieldset>
</form>
EOT;
        $this->getResponse()->setBody($xml);
    }

    /**
     * Customer edit address form
     */
    public function editAddressAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }

        $address = Mage::getModel('customer/address');
        // Init address object
        if ($id = $this->getRequest()->getParam('id')) {
            $address->load($id);
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_message($this->__('Specified address does not exist.'), self::MESSAGE_STATUS_ERROR);
                return ;
            }
        }

        $collection = Mage::getModel('directory/country')->getResourceCollection()
            ->loadByStore();

        $xmlModel   = new Varien_Simplexml_Element('<node></node>');
        $firstname  = $xmlModel->xmlentities(strip_tags($address->getFirstname()));
        $lastname   = $xmlModel->xmlentities(strip_tags($address->getLastname()));
        $company    = $xmlModel->xmlentities(strip_tags($address->getCompany()));
        $street1    = $xmlModel->xmlentities(strip_tags($address->getStreet(1)));
        $street2    = $xmlModel->xmlentities(strip_tags($address->getStreet(2)));
        $city       = $xmlModel->xmlentities(strip_tags($address->getCity()));
        $regionId   = $xmlModel->xmlentities($address->getRegionId());
        $region = Mage::getModel('directory/region')->load($regionId)->getName();
        if (!$region) {
            $region = $address->getRegion();
        }
        $region     = $xmlModel->xmlentities(strip_tags($region));
        $postcode   = $xmlModel->xmlentities(strip_tags($address->getPostcode()));
        $countryId  = $xmlModel->xmlentities($address->getCountryId());
        $telephone  = $xmlModel->xmlentities(strip_tags($address->getTelephone()));
        $fax        = $xmlModel->xmlentities(strip_tags($address->getFax()));

        $countries = $this->_getCountryOptions();

        $regions = array();
        $countryOptionsXml = '<values>';
        if (is_array($countries)) {
            foreach ($countries as $key => $data) {
                if ($data['value']) {
                    $regions = $this->_getRegionOptions($data['value']);
                }
                $countryOptionsXml .= '
                <item relation="' . (is_array($regions) && !empty($regions) ? 'region_id' : 'region') . '"' . ($countryId == $data['value'] ? ' selected="1"' : '') . '>
                    <label>' . $xmlModel->xmlentities((string)$data['label']) . '</label>
                    <value>' . $xmlModel->xmlentities($data['value']) . '</value>';
                if (is_array($regions) && !empty($regions)) {
                    $countryOptionsXml .= '<regions>';
                    foreach ($regions as $_key => $_data){
                        $countryOptionsXml .= '<region_item' . ($regionId == $_data['value'] ? ' selected="1"' : '') . '>';
                        $countryOptionsXml .=
                            '<label>' . $xmlModel->xmlentities((string)$_data['label']) . '</label>
                             <value>' . $xmlModel->xmlentities($_data['value']) . '</value>';
                        $countryOptionsXml .= '</region_item>';
                    }
                    $countryOptionsXml .= '</regions>';
                }
                $countryOptionsXml .= '</item>';
            }
        }
        $countryOptionsXml .= '</values>';

        $xml = <<<EOT
<?xml version="1.0"?>
<form name="address_form" method="post">
    <fieldset legend="Contact Information">
        <field name="firstname" type="text" label="First Name" required="true" value="$firstname">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="lastname" type="text" label="Last Name" required="true" value="$lastname">
            <validators>
                <validator type="regexp" message="Letters only">^[ a-zA-Z]+$</validator>
            </validators>
        </field>
        <field name="company" type="text" label="Company" value="$company" />
        <field name="telephone" type="text" label="Telephone" required="true" value="$telephone" />
        <field name="fax" type="text" label="Fax" value="$fax" />
    </fieldset>
    <fieldset legend="Address">
        <field name="street[]" type="text" label="Street Address" required="true" value="$street1" />
        <field name="street[]" type="text" value="$street2" />
        <field name="city" type="text" label="City" required="true" value="$city" />
        <field name="region" type="text" label="State/Province" value="$region" />
        <field name="region_id" type="select" label="State/Province" required="true" />
        <field name="postcode" type="text" label="Zip/Postal Code" required="true" value="$postcode" />
        <field name="country_id" type="select" label="Country" required="true">
            $countryOptionsXml
        </field>
        <field name="default_billing" type="checkbox" label="Use as my default billing address"/>
        <field name="default_shipping" type="checkbox" label="Use as my default shipping address"/>
    </fieldset>
</form>
EOT;
        $this->getResponse()->setBody($xml);
    }

    /**
     * Remove customer address
     */
    public function deleteAddressAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }

        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_message($this->__('The address does not belong to this customer.'), self::MESSAGE_STATUS_ERROR);
                return;
            }

            try {
                $address->delete();
                $this->_message($this->__('The address has been deleted.'), self::MESSAGE_STATUS_SUCCESS);
            }
            catch (Exception $e){
                $this->_message($this->__('An error occurred while deleting the address.'), self::MESSAGE_STATUS_ERROR);
            }
        }
    }

    /**
     * Add/Save customer address
     */
    public function saveAddressAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }

        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')
                ->setData($this->getRequest()->getPost())
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $customerAddress = $this->_getSession()->getCustomer()->getAddressById($addressId);
                if ($customerAddress->getId() && $customerAddress->getCustomerId() == $this->_getSession()->getCustomerId()) {
                    $address->setId($addressId);
                }
                else {
                    $address->setId(null);
                }
            }
            else {
                $address->setId(null);
            }
            try {
                $addressValidation = $address->validate();
                if (true === $addressValidation) {
                    $address->save();
                    $this->_message($this->__('The address has been saved.'), self::MESSAGE_STATUS_SUCCESS);
                    return;
                }
                else {
                    if (is_array($addressValidation)) {
                        $this->_message(implode('. ', $addressValidation), self::MESSAGE_STATUS_ERROR);
                    }
                    else {
                        $this->_message($this->__('Cannot save address.'), self::MESSAGE_STATUS_ERROR);
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
            }
            catch (Exception $e) {
                $this->_message($this->__('Cannot save address.'), self::MESSAGE_STATUS_ERROR);
            }
        }
        else {
            $this->_message($this->__('Adddress data not specified.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

    /**
     * Customer orders list
     */
    public function orderListAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }

        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Check if customer is loggined
     */
    public function isLogginedAction()
    {
        $message = new Varien_Simplexml_Element('<message></message>');
        $message->addChild('is_loggined', (int)$this->_getSession()->isLoggedIn());
        $this->getResponse()->setBody($message->asNiceXml());
    }

}
