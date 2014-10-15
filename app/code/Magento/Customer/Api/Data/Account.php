<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api\Data;

interface Account
{
    /**
     * @return \Magento\Customer\Api\Data\Customer
     */
    public function getCustomer();

    /**
     * @param \Magento\Customer\Api\Data\Customer $customer
     * @return $this
     */
    public function setCustomer(\Magento\Customer\Api\Data\Customer $customer);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password);
} 
