<?php
/**
 * SID resolver interface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Sesstion
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Session;

interface SidResolverInterface
{
    /**
     * @return string
     */
    public function getSid();

    /**
     * Get session id query param
     *
     * @return string
     */
    public function getSessionIdQueryParam();
}
