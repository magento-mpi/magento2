<?php
/**
 * Class CreateCustomerAccountResponse
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Response;

class CreateCustomerAccountResponseBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param int $customerId
     * @return CreateCustomerAccountResponseBuilder
     */
    public function setCustomerId($customerId)
    {
        return $this->_set('customer_id', $customerId);
    }

    /**
     * @param string $status
     * @return CreateCustomerAccountResponseBuilder
     */
    public function setStatus($status)
    {
        return $this->_set('status', $status);
    }
}
