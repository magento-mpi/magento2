<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Cache;

class InstanceFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get cache instance model
     *
     * @param string $instanceName
     * @return \Magento\Cache\FrontendInterface
     * @throws \UnexpectedValueException
     */
    public function get($instanceName)
    {
        $instance = $this->_objectManager->get($instanceName);
        if (!$instance instanceof \Magento\Cache\FrontendInterface) {
            throw new \UnexpectedValueException("Cache type class '{$instanceName}' has to be a cache frontend.");
        }

        return $instance;
    }
}
