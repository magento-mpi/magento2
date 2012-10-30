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
 * Webservice apia2 REST route
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Router_Route_Rest extends Mage_Webapi_Controller_Router_RouteAbstract
{
    const VERSION_PARAM_NAME = 'resourceVersion';

    /** @var string */
    protected $_resourceName;

    /** @var string */
    protected $_resourceType;

    /**
     * Set route resource
     *
     * @param string $resourceName
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setResourceName($resourceName)
    {
        $this->_resourceName = $resourceName;
        return $this;
    }

    /**
     * Get route resource
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    /**
     * Set route resource type
     *
     * @param string $resourceType
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;
        return $this;
    }

    /**
     * Get route resource type
     *
     * @return string
     */
    public function getResourceType()
    {
        return $this->_resourceType;
    }
}
