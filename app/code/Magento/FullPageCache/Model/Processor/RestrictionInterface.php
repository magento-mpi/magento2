<?php
/**
 * Page cache processor restriction interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Processor;

interface RestrictionInterface
{
    /**
     * Cookie key name for disabling FPC
     */
    const NO_CACHE_COOKIE = 'NO_CACHE';

    /**
     * Check if processor is allowed for current HTTP request.
     *
     * @param string $requestId
     * @return bool
     */
    public function isAllowed($requestId);

    /**
     * Set is denied mode for FPC processors
     */
    public function setIsDenied();
}
