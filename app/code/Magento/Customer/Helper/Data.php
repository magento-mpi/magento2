<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper;

/**
 * Customer Data Helper
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Customer group cache context
     */
    const CONTEXT_GROUP = 'customer_group';

    /**
     * Customer authorization cache context
     */
    const CONTEXT_AUTH = 'customer_logged_in';
}
