<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

/**
 * Interface CustomerCurrentServiceInterface
 */
interface CustomerCurrentServiceInterface
{
    /**
     * Returns current customer according to session and context
     *
     * @return Dto\Customer
     */
    public function getCustomer();

    /**
     * Returns customer id from session
     *
     * @return int|null
     */
    public function getCustomerId();
} 