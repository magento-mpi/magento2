<?php
/**
 * No route handler interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Router_NoRouteHandlerInterface
{
    /**
     * Check and process no route request
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function process(Mage_Core_Controller_Request_Http $request);
}
