<?php
/**
 * Abstract dispatcher for web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Webapi_Controller_DispatcherInterface
{
    /**
     * Dispatch request.
     *
     * @return Mage_Webapi_Controller_DispatcherInterface
     */
    public function dispatch();
}
