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
    const SESSION_ID_QUERY_PARAM = 'SID';

    /**
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @return string
     */
    public function getSid(\Magento\Core\Model\Session\AbstractSession $session);

    /**
     * Get session id query param
     *
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @return string
     */
    public function getSessionIdQueryParam(\Magento\Core\Model\Session\AbstractSession $session);
}
