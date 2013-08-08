<?php
/**
 * Page cache processor restriction model.
 * Check if processor is allowed for current HTTP request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PageCache_Model_Processor_Restriction
    extends Enterprise_PageCache_Model_Processor_Restriction
{
    /**
     * Check if processor is allowed for current HTTP request.
     *
     * @param string $requestId
     * @return bool
     */
    public function isAllowed($requestId)
    {
        if (true === $this->_isDenied || !$requestId) {
            return false;
        }

        if ('on' === $this->_environment->getServer('HTTPS')) {
            return false;
        }

        if ($this->_environment->hasQuery(Magento_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM)) {
            return false;
        }

        if (!$this->_cache->canUse('full_page')) {
            return false;
        }

        return true;
    }
}
