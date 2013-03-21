<?php
/**
 * Abstract API service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Abstract
{
    /**
     * Returns unique service identifier.
     *
     * @return string
     */
    abstract protected function _getServiceId();

    /**
     * Returns unique service method identifier.
     *
     * @param string $methodName
     * @return string
     */
    public function getMethodId($methodName)
    {
        return sprintf('%s/%s', $this->_getServiceId(), $methodName);
    }
}
