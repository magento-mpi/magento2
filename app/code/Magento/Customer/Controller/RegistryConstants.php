<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller;

/**
 * Customer reviews controller
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */


class RegistryConstants
{
    /**
     * Registry key where current customer DTO stored
     * @todo switch to use ID instead and remove after refactoring of all occurrences
     */
    const CURRENT_CUSTOMER = 'current_customer';

    /**
     * Registry key where current customer ID is stored
     */
    const CURRENT_CUSTOMER_ID = 'current_customer_id';

    /**
     * Registry key where current CustomerGroup ID is stored
     */
    const CURRENT_GROUP_ID = 'current_group_id';

}