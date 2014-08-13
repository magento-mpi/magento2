<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order\Customer;


class Builder
{
    protected $objectManager;
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
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param $customerDob
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setDob($customerDob)
    {
        $this->customerDob = $customerDob;
        return $this;
    }

    /**
     * @param $customerEmail
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * @param $customerFirstName
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setFirstName($customerFirstName)
    {
        $this->customerFirstName = $customerFirstName;
        return $this;
    }

    /**
     * @param $customerGender
     * @return mixed
     */
    public function setGender($customerGender)
    {
        $this->customerGender = $customerGender;
        return $customerGender;
    }

    /**
     * @param $customerGroupId
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;
        return $this;
    }

    /**
     * @param $customerId
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setId($customerId)
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @param $customerIsGuest
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setIsGuest($customerIsGuest)
    {
        $this->customerIsGuest = $customerIsGuest;
        return $this;
    }

    /**
     * @param $customerLastName
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setLastName($customerLastName)
    {
        $this->customerLastName = $customerLastName;
        return $this;
    }

    /**
     * @param $customerMiddleName
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setMiddleName($customerMiddleName)
    {
        $this->customerMiddleName = $customerMiddleName;
        return $this;
    }

    /**
     * @param $customerNote
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setNote($customerNote)
    {
        $this->customerNote = $customerNote;
        return $this;
    }

    /**
     * @param $customerNoteNotify
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setNoteNotify($customerNoteNotify)
    {
        $this->customerNoteNotify = $customerNoteNotify;
        return $this;
    }

    /**
     * @param $customerPrefix
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setPrefix($customerPrefix)
    {
        $this->customerPrefix = $customerPrefix;
        return $this;
    }

    /**
     * @param $customerSuffix
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setSuffix($customerSuffix)
    {
        $this->customerSuffix = $customerSuffix;
        return $this;
    }

    /**
     * @param $customerTaxvat
     * @return \Magento\Sales\Model\Order\Customer\Builder
     */
    public function setTaxvat($customerTaxvat)
    {
        $this->customerTaxvat = $customerTaxvat;
        return $this;
    }

    /**
     * @return \Magento\Sales\Model\Order\Customer
     */
    public function create()
    {
        return $this->objectManager->create('Magento\Sales\Model\Order\Customer', [
            'customerDob' => $this->customerDob,
            'customerEmail' => $this->customerEmail,
            'customerFirstName' => $this->customerFirstName,
            'customerGender' => $this->customerGender,
            'customerGroupId' => $this->customerGroupId,
            'customerId' => $this->customerId,
            'customerIsGuest' => $this->customerIsGuest,
            'customerLastName' => $this->customerLastName,
            'customerMiddleName' => $this->customerMiddleName,
            'customerNote' => $this->customerNote,
            'customerNoteNotify' => $this->customerNoteNotify,
            'customerPrefix' => $this->customerPrefix,
            'customerSuffix' => $this->customerSuffix,
            'customerTaxvat' => $this->customerTaxvat
        ]);
    }
}
