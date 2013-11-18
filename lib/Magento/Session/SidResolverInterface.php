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
     * Session ID in query param
     */
    const SESSION_ID_QUERY_PARAM        = 'SID';

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
