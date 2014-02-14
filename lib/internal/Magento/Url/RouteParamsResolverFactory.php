<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Url;

class RouteParamsResolverFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_instanceName;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Url\RouteParamsResolverInterface'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create RouteParamsResolverInterface instance
     *
     * @param array $data
     * @return \Magento\Url\RouteParamsResolverInterface
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
