<?php
/**
 * Abstract dispatcher for web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Webapi_Controller_DispatcherInterface
{
    /**
     * Dispatch request.
     *
     * @return Magento_Webapi_Controller_DispatcherInterface
     */
    public function dispatch();
}
