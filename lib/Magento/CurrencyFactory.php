<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento;

class CurrencyFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * @var string
     */
    protected $_instanceName = null;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, $instanceName = 'Magento\CurrencyInterface')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\CurrencyInterface
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
