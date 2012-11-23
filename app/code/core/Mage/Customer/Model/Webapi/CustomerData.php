<?php
/**
 * Data structure description for customer entity
 * {myAppInfo:hello}
 * {seeLink:http://wiki.magento.com/:link title:link for description}
 *
 * @copyright {}
 */
class Mage_Customer_Model_Webapi_CustomerData
{
    /**
     * {summary:This is a summary for email.<br/>
     * <b>multiline</b>}
     * Customer email.
     * Use this field to set customer <b>email</b>!
     * {maxLength:100}
     * {tagStatus:Reserved}
     * {seeLink:www.google.com:link title:link for description}
     *
     * @var string
     */
    public $email;

    /**
     * Customer first name
     * {maxLength:255}
     * {callInfo:customerUpdate:requiredInput:conditionally}
     * {callInfo:customerCreate:requiredInput:yes}
     *
     * @var string
     * @optional
     */
    public $firstname;

    /**
     * Customer Balance.
     * {docInstructions:input:noDoc}
     * {docInstructions:output:noDoc}
     *
     * @var int {min:1}{max:100}
     */
    public $balance;

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
     * @var Mage_Customer_Model_Webapi_Customer_AddressDataa
     */
    public $address;
}
