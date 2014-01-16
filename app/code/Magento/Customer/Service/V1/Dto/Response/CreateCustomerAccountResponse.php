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

class CreateCustomerAccountResponse extends \Magento\Service\Entity\AbstractDto
{
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
}
