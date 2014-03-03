<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Session;

use Magento\Session\SessionManagerInterface;

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
     * @return SessionManagerInterface
     */
    public function get($instanceName, $data = array())
    {
        $object = $this->_objectManager->get($instanceName, array('data' => $data));
        if (!$object instanceof SessionManagerInterface) {
            throw new \LogicException($instanceName . ' doesn\'t implement \Magento\Session\SessionManagerInterface');
        }

        return $object;
    }
}
