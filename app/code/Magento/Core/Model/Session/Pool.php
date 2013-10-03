<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Session;

class Pool
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
     * Create instance
     *
     * @param string $instanceName
     * @param array $data
     * @throws \LogicException
     * @return \Magento\Core\Model\Session\AbstractSession
     */
    public function get($instanceName, $data = array())
    {
        $object = $this->_objectManager->get($instanceName, array('data' => $data));
        if (!$object instanceof \Magento\Core\Model\Session\AbstractSession) {
            throw new \LogicException($instanceName . ' doesn\'t extend \Magento\Core\Model\Session\AbstractSession');
        }

        return $object;
    }
}
