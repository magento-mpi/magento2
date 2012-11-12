<?php
/**
 * Class fpr API routes.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Router_Route_ApiType extends Mage_Webapi_Controller_Router_RouteAbstract
{
    const PARAM_API_TYPE = 'api_type';
    const API_AREA_NAME = 'api';

    public static function getApiRoute()
    {
        return self::API_AREA_NAME . '/:' . self::PARAM_API_TYPE;
    }
}
