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
 * Webservice Webapi data helper
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Request interpret adapters
     */
    const XML_PATH_Webapi_REQUEST_INTERPRETERS = 'global/webapi/request/interpreters';

    /**
     * Response render adapters
     */
    const XML_PATH_Webapi_RESPONSE_RENDERS     = 'global/webapi/response/renders';

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getRequestInterpreterAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_Webapi_REQUEST_INTERPRETERS);
    }

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getResponseRenderAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_Webapi_RESPONSE_RENDERS);
    }
}
