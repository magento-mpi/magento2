<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Model\Currency\Import;

/**
 * Import currency model factory
 */
class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $_coreConfig;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\ConfigInterface $coreConfig
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\ConfigInterface $coreConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Create new import object
     *
     * @param string $service
     * @param array $data
     * @throws \InvalidArgumentException
     * @return \Magento\Directory\Model\Currency\Import\Interface
     */
    public function create($service, array $data = array())
    {
        $serviceClass = $this->_coreConfig->getNode('global/currency/import/services/' . $service . '/model')
            ->asArray();
        $service = $this->_objectManager->create($serviceClass, $data);
        if (false == ($service instanceof \Magento\Directory\Model\Currency\Import\ImportInterface)) {
            throw new \InvalidArgumentException(
                $serviceClass . ' doesn\'t implement \Magento\Directory\Model\Currency\Import\Interface'
            );
        }
        return $service;
    }
}
