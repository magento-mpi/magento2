<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
 */
namespace Magento\Sales\Model\Quote\Address;

class TotalFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Quote address factory constructor
     *
     * @param \Magento\ObjectManager $objManager
     */
    public function __construct(\Magento\ObjectManager $objManager)
    {
        $this->_objectManager = $objManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
     */
    public function create($instanceName, array $data = array())
    {
        return $this->_objectManager->create($instanceName, $data);
    }
}
