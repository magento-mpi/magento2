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
 * Factory class for \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
 */
namespace Magento\Sales\Model\Order\Pdf;

class ItemsFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     */
    public function get($instanceName, array $data = array())
    {
        return $this->_objectManager->get($instanceName, $data);
    }
}
