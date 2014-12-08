<?php
/**
 * Factory for \Magento\Integration\Model\Integration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Integration;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of \Magento\Integration\Model\Integration
     *
     * @param array $data Data for integration
     * @return \Magento\Integration\Model\Integration
     */
    public function create(array $data = [])
    {
        $integration = $this->_objectManager->create('Magento\Integration\Model\Integration', []);
        $integration->setData($data);
        return $integration;
    }
}
