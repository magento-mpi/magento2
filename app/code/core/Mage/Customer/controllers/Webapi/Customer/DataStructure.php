<?php
/**
 * Data structure description for customer entity
 *
 * @copyright {}
 */
class Mage_Customer_Webapi_Customer_DataStructure
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
     * @var Mage_Customer_Webapi_Customer_Address_DataStructure
     */
    public $address;
}
