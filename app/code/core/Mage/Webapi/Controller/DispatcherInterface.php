<?php
/**
 * Abstract dispatcher for web API requests.
 *
 * @copyright {}
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
