<?php
/**
 * Factory for \Magento\Core\Model\DataService\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService\Config\Reader;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new \Magento\Core\Model\DataService\Config\Reader by array of configuration files
     *
     * @param array $configFiles
     * @return \Magento\Core\Model\DataService\Config\Reader
     */
    public function createReader(array $configFiles)
    {
        return $this->_objectManager->create('Magento\Core\Model\DataService\Config\Reader',
            array('configFiles'  => $configFiles));
    }
}
