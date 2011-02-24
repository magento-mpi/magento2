<?php

/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Customer extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->Data = array();
    }

    /**
     * Fill Account Info Tab
     *
     * @param <type> $params
     */
    public function fillAccountInfoTab($params)
    {
        $this->setUiNamespace('admin/pages/customers/manage_customers/create_customer');
        $this->click($this->getUiElement('tabs/account_info'));

        $this->checkAndSelectField($params, 'associate_website');
        $this->checkAndSelectField($params, 'customer_group');
        $this->checkAndFillField($params, 'prefix', NULL);
        $this->checkAndFillField($params, 'first_name', NULL);
        $this->checkAndFillField($params, 'middle_name', NULL);
        $this->checkAndFillField($params, 'last_name', NULL);
        $this->checkAndFillField($params, 'suffix', NULL);
        $this->checkAndFillField($params, 'email', NULL);
        $this->checkAndFillField($params, 'date_of_birth', NULL);
        $this->checkAndFillField($params, 'tax_vat_number', NULL);
        $this->checkAndSelectField($params, 'gender');
        $xpath = $this->getUiElement('selectors/send_welcome_email_from');
        $sendEmail = $this->isSetValue($params, 'send_welcome_email');
        $sendEmailFromValue = $this->isSetValue($params, 'send_welcome_email_from');
        if ($this->isElementPresent($xpath) and $sendEmail == 'Yes') {
            $this->click($this->getUiElement('inputs/send_welcome_email'));
            $optionXpath = $this->getUiElement('/admin/global/elements/option_for_field', $sendEmailFromValue);
            if ($this->isElementPresent($xpath . $optionXpath)) {
                $this->select($xpath, "label=regexp:\\s+" . $sendEmailFromValue);
            } else {
                $this->printInfo("The value '" . $value . "' cannot be set for the field '" . $field . "'");
            }
        } elseif ($sendEmail == 'Yes') {
            $this->printInfo("You cannot select option 'Send Welcome Email' because 'Associate to Website'='Admin'");
        }
        if ($this->isSetValue($params, 'password') == 'generate') {
            $this->click($this->getUiElement('inputs/generate_password'));
        } else {
            $this->checkAndFillField($params, 'password', NULL);
        }
    }

    /**
     * Add new address for customer
     *
     * @param <type> $params
     */
    public function fillAddressForm($params)
    {
        $searchWord = '/^address_/';
        $addressData = $this->dataPreparation($params, $searchWord);
        if (count($addressData) > 0) {
            $this->setUiNamespace('admin/pages/customers/manage_customers/create_customer');
            $this->click($this->getUiElement('tabs/addresses'));
            $qtyAddress = $this->getXpathCount($this->getUiElement('elements/address_list'));
            $qtyAddress += 1;
            $this->click($this->getUiElement('buttons/add_new_address'));
            foreach ($addressData as $key => $value) {
                if (preg_match('/country/', $key)) {
                    if (!$this->isElementPresent($this->getUiElement('selectors/' . $key, $qtyAddress) .
                                    $this->getUiElement('/admin/global/elements/selected_option', $value))) {
                        $this->select($this->getUiElement('selectors/' . $key, $qtyAddress), $value);
                        $this->pleaseWait();
                    }
                } elseif (preg_match('/state/', $key)) {
                    if ($this->isElementPresent($this->getUiElement('inputs/' . $key, $qtyAddress))) {
                        $this->type($this->getUiElement('inputs/' . $key, $qtyAddress), $value);
                    } elseif ($this->isElementPresent($this->getUiElement('selectors/' . $key, $qtyAddress))) {
                        $this->select($this->getUiElement('selectors/' . $key, $qtyAddress), $value);
                    }
                } else {
                    $this->type($this->getUiElement('inputs/' . $key, $qtyAddress), $value);
                }
            }
            $this->setDefaultAddress($params, $qtyAddress);
        }
    }

    /**
     *
     *
     * @param <type> $params
     * @return int
     */
    public function isAddressPresent($params)
    {
        $xpath = $this->getUiElement('elements/address_list');
        $qtyAddress = $this->getXpathCount($xpath);
        $allAdress = $this->dataPreparation($params, '/^address_/');
        $adress = array();
        foreach ($allAdress as $key => $value) {
            if (preg_match("/(first_name)|(last_name)|(company)|(strreet)|(city)
                |(state)|(zip_code)|(country)|(tel)|(fax)/", $key)) {
                $adress[$key] = preg_quote($value);
            }
        }
        $res = 0;
        for ($i = 1; $i <= $qtyAddress; $i++) {
            $addressValue = $this->getText($xpath . "[$i]/address");
            $addressValue = str_replace("\n", ' ', $addressValue);
            foreach ($adress as $v) {
                $res += preg_match("/$v/", $addressValue);
            }
            if ($res == count($adress)) {
                $res = $addressValue;
                $this->printInfo('Address is present');
                return $i;
            }
            $res = 0;
        }
        $this->printInfo('This address is not present');
        return -1;
    }

    /**
     *  set Default Billing Address or(and) Default Shipping Address
     *
     * @param <type> $params
     * @param <type> $addressItemNumber
     * @return boolean
     */
    public function setDefaultAddress($params, $addressItemNumber)
    {
        $result = FALSE;
        $xpath = $this->getUiElement('elements/address_list') . "[$addressItemNumber]";
        $isDefault = $this->isSetValue($params, 'use_as_default');
        if (is_array($isDefault)) {
            foreach ($isDefault as $value) {
                if ($this->isElementPresent($xpath . $this->getUiElement('inputs/use_def_' . $value))) {
                    $this->click($xpath . $this->getUiElement('inputs/use_def_' . $value));
                    $result = TRUE;
                }
            }
        } elseif ($isDefault != NULL) {
            if ($this->isElementPresent($xpath . $this->getUiElement('inputs/use_def_' . $isDefault))) {
                $this->click($xpath . $this->getUiElement('inputs/use_def_' . $isDefault));
                $result = TRUE;
            }
        }
        return $result;
    }

    /**
     * create customer
     *
     * @param <type> $params
     */
    public function doCreateCustomer($params)
    {
        $this->navigate('Customers/Manage Customers');

        $this->setUiNamespace('admin/pages/customers/manage_customers/');
        $this->clickAndWait($this->getUiElement('buttons/add_new_customer'));
        $this->fillAccountInfoTab($params);
        $this->fillAddressForm($params);
        $this->saveAndVerifyForErrors();
    }

    /**
     * Add Address for exist customer
     *
     * @param <type> $params
     */
    public function doAddAddress($params)
    {
        $this->navigate('Customers/Manage Customers');

        $searchWord = '/^search_user_/';
        $this->setUiNamespace('admin/pages/customers/manage_customers/');
        $searchElements = $this->dataPreparation($params, $searchWord);
        $searchRes = $this->searchAndDoAction('customer_container', $searchElements, 'open', NULL);
        if ($searchRes) {
            $this->setUiNamespace('admin/pages/customers/manage_customers/create_customer');
            $this->click($this->getUiElement('tabs/addresses'));
            $isPresent = $this->isAddressPresent($params);
            if ($isPresent != -1) {
                $addressItemNumber = $isPresent;
                $result = $this->setDefaultAddress($params, $addressItemNumber);
            }
            if ($isPresent == -1) {
                $this->fillAddressForm($params);
                $addressItemNumber = 1;
                $this->setDefaultAddress($params, $addressItemNumber);
                $result = TRUE;
            }
            if ($result) {
                $this->saveAndVerifyForErrors();
            }
        }
    }

    /**
     *
     * @param <type> $params
     * @param <type> $whatToDelete
     */
    public function doDelete($params, $whatToDelete)
    {
        $this->navigate('Customers/Manage Customers');

        $result = TRUE;
        $searchWord = '/^search_user_/';
        $this->setUiNamespace('admin/pages/customers/manage_customers/');
        $searchElements = $this->dataPreparation($params, $searchWord);
        $searchRes = $this->searchAndDoAction('customer_container', $searchElements, 'open', NULL);
        if ($searchRes) {
            $this->setUiNamespace('admin/pages/customers/manage_customers/create_customer');
            switch ($whatToDelete) {
                case 'address':
                    $this->click($this->getUiElement('tabs/addresses'));
                    $isPresent = $this->isAddressPresent($params);
                    if ($isPresent != -1) {
                        $xpath = $this->getUiElement('elements/address_list') . "[$isPresent]" .
                                $this->getUiElement('buttons/delete_address');
                        if ($this->isElementPresent($xpath)) {
                            $this->chooseCancelOnNextConfirmation();
                            $this->click($xpath);
                            if ($this->isConfirmationPresent()) {
                                $text = $this->getConfirmation();
                                if ($text == 'Are you sure you want to delete this address?') {
                                    $this->chooseOkOnNextConfirmation();
                                    $this->click($xpath);
                                    $this->getConfirmation();
                                } else {
                                    $this->printInfo('The confirmation text incorrect: ' . $text);
                                    $result = FALSE;
                                }
                            } else {
                                $this->printInfo('The confirmation does not appear');
                            }
                            if ($result) {
                                $this->saveAndVerifyForErrors();
                            }
                        } else {
                            $this->printInfo('This address cannot be deleted');
                        }
                    }
                    break;
                case 'customer':
                    $this->doDeleteElement();
                    break;
            }
        }
    }

}