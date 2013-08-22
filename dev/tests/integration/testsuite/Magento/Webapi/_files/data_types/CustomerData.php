<?php
/**
 * Tests fixture for Auto Discovery functionality. Customer entity.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Vendor_Module_Model_Webapi_CustomerData
{
    /**
     * Customer email.
     * Use this field to set customer <b>email</b>!
     *
     * @var string
     */
    public $email;

    /**
     * Customer first name
     *
     * @var string
     */
    public $firstname;

    /**
     * Customer last name
     *
     * @var string
     */
    public $lastname = 'DefaultLastName';

    /**
     * Customer password
     *
     * @var string
     */
    public $password = '123123q';

    /**
     * Customer address
     *
     * @optional true
     * @var Vendor_Module_Model_Webapi_Customer_AddressData
     */
    public $address;
}
