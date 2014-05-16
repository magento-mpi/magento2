<?php
/**
 * Factory for \Magento\Integration\Model\Integration
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Integration;

class Factory
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
     * Create a new instance of \Magento\Integration\Model\Integration
     *
     * @param array $data Data for integration
     * @return \Magento\Integration\Model\Integration
     */
    public function create(array $data = array())
    {
        $integration = $this->_objectManager->create('Magento\Integration\Model\Integration', array());
        $integration->setData($data);
        return $integration;
    }
}
