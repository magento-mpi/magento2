<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice Webapi Route to find out API type from request
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Router_Route_ApiType extends Mage_Webapi_Controller_Router_RouteAbstract
{
    /**
     * API url template with API type variable
     */
    const API_ROUTE = 'api/:api_type';

    /**
     * Prepares the route for mapping by splitting (exploding) it
     * to a corresponding atomic parts. These parts are assigned
     * a position which is later used for matching and preparing values.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param array $reqs Regular expression requirements for variables (keys as variable names)
     * @param Zend_Translate $translator Translator to use for this instance
     * @param mixed $locale
     */
    public function __construct($route = null, $defaults = array(), $reqs = array(), Zend_Translate $translator = null,
        $locale = null
    ) {
        parent::__construct(self::API_ROUTE, $defaults, $reqs, $translator, $locale);
    }
}
