<?php
/**
 * Route to resources available via REST API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Router\Route;

class Rest extends \Magento\Webapi\Controller\Router\Route
{
    /**#@+
     * Names of special parameters in routes.
     */
    const PARAM_VERSION = 'resourceVersion';
    const PARAM_ID = 'id';
    const PARAM_PARENT_ID = 'parentId';
    /**#@-*/

    /** @var string */
    protected $_resourceName;

    /** @var string */
    protected $_resourceType;

    /**
     * Set route resource.
     *
     * @param string $resourceName
     * @return \Magento\Webapi\Controller\Router\Route\Rest
     */
    public function setResourceName($resourceName)
    {
        $this->_resourceName = $resourceName;
        return $this;
    }

    /**
     * Get route resource.
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    /**
     * Set route resource type.
     *
     * @param string $resourceType
     * @return \Magento\Webapi\Controller\Router\Route\Rest
     */
    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;
        return $this;
    }

    /**
     * Get route resource type.
     *
     * @return string
     */
    public function getResourceType()
    {
        return $this->_resourceType;
    }
}
