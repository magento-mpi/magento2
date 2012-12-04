<?php
/**
 * Route to Magento web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Router_Route_Webapi extends Mage_Webapi_Controller_Router_RouteAbstract
{
    const PARAM_API_TYPE = 'api_type';
    const API_AREA_NAME = 'api';

    /**
     * Retrieve API route.
     *
     * @return string
     */
    public static function getApiRoute()
    {
        return self::API_AREA_NAME . '/:' . self::PARAM_API_TYPE;
    }
}
