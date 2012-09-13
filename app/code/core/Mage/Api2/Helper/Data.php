<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice API2 data helper
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Request interpret adapters
     */
    const XML_PATH_API2_REQUEST_INTERPRETERS = 'global/api2/request/interpreters';

    /**
     * Response render adapters
     */
    const XML_PATH_API2_RESPONSE_RENDERS     = 'global/api2/response/renders';

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getRequestInterpreterAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_API2_REQUEST_INTERPRETERS);
    }

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getResponseRenderAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_API2_RESPONSE_RENDERS);
    }
}
