<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order;


class Customer
{
    /** @var string  */
    protected $customerDob;
    protected $customerEmail;
    protected $customerFirstName;
    protected $customerGender;
    protected $customerGroupId;
    protected $customerId;
    protected $customerIsGuest;
    protected $customerLastName;
    protected $customerMiddleName;
    protected $customerNote;
    protected $customerNoteNotify;
    protected $customerPrefix;
    protected $customerSuffix;
    protected $customerTaxvat;

    /**
     * @param string $customerDob
     * @param string $customerEmail
     * @param string $customerFirstName
     * @param string $customerGender
     * @param string $customerGroupId
     * @param int $customerId
     * @param int $customerIsGuest
     * @param string $customerLastName
     * @param string $customerMiddleName
     * @param string $customerNote
     * @param string $customerNoteNotify
     * @param string $customerPrefix
     * @param string $customerSuffix
     * @param string $customerTaxvat
     */
    public function __construct($customerDob, $customerEmail, $customerFirstName, $customerGender, $customerGroupId,
        $customerId, $customerIsGuest, $customerLastName, $customerMiddleName, $customerNote, $customerNoteNotify,
        $customerPrefix, $customerSuffix, $customerTaxvat
    ) {
        $this->customerDob = $customerDob;
        $this->customerEmail = $customerEmail;
        $this->customerFirstName = $customerFirstName;
        $this->customerGender = $customerGender;
        $this->customerGroupId = $customerGroupId;
        $this->customerId = $customerId;
        $this->customerIsGuest = $customerIsGuest;
        $this->customerLastName = $customerLastName;
        $this->customerMiddleName = $customerMiddleName;
        $this->customerNote = $customerNote;
        $this->customerNoteNotify = $customerNoteNotify;
        $this->customerPrefix = $customerPrefix;
        $this->customerSuffix = $customerSuffix;
        $this->customerTaxvat = $customerTaxvat;
    }

    /**
     * @return string
     */
    public function getDob()
    {
        return $this->customerDob;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->customerFirstName;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->customerGender;
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function getIsGuest()
    {
        return $this->customerIsGuest;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->customerLastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->customerMiddleName;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->customerNote;
    }

    /**
     * @return string
     */
    public function getNoteNotify()
    {
        return $this->customerNoteNotify;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->customerPrefix;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->customerSuffix;
    }

    /**
     * @return string
     */
    public function getTaxvat()
    {
        return $this->customerTaxvat;
    }
}

