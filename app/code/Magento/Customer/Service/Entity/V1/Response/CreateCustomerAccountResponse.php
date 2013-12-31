<?php
/**
 * Class CreateCustomerAccountResponse
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1\Response;

use Magento\Service\Entity\AbstractDto;

class CreateCustomerAccountResponse extends AbstractDto
{
    /**
     * @param int $customerId
     * @param string $status
     */
    public function __construct($customerId, $status)
    {
        parent::__construct();
        $this->setCustomerId($customerId);
        $this->setStatus($status);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get('customer_id');
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_get('status');
    }

    /**
     * @param int $customerId
     * @return CreateCustomerAccountResponse
     */
    public function setCustomerId($customerId)
    {
        return $this->_set('customer_id', $customerId);
    }

    /**
     * @param string $status
     * @return CreateCustomerAccountResponse
     */
    public function setStatus($status)
    {
        return $this->_set('status', $status);
    }
}
