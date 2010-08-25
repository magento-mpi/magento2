<?php
/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Customer_Address_Add extends Model_Admin_Customer_Address
{
    /**
     * Run single test: Login, Delete previously added address if exists, add address
     * @param CustID - ID customer
     * @param TestID - address with TestID in the First Name field will be used
     * @param boolean isBilling - if set, address will be checked as dafault billing address
     * @param boolean isShipping - if set, address will be checked as dafault shipping address
     * @dataProvider aaa
     */
    function probeAddressCreation($subTestID, $isBilling, $isShipping) {
        $this->doLogin($this->baseUrl, $this->userName, $this->password);

        $firstName = $this->testId . $subTestID;

        $params = array(
            "Prefix"      => "Prefix Sample Value",
            "First Name"  => $firstName,
            "Last Name"   => "Lname Sample Value",
            "Middle Name" => "Mname Sample Value",
            "Suffix"      => "Suffix Sample Value",
            "Company"     => "Company Sample Value",
            "City"        => "City Sample Value",
            "Zip"         => "Zip Sample Value",
            "Telephone"   => "Telephone Sample Value",
            "Fax"         => "Fax Sample Value",
        );

        if ($this->delAddresses($firstName)) {
            if ($this->addAddress($params, $isBilling, $isShipping)) {
                $this->verifyAddress($params, $isBilling, $isShipping);
            }
        }
    }

    public function aaa()
    {
        return array(
            araay(1, true, true),
            araay(2, true, true),
            araay(3, true, true),
        );
    }


}

