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
     * @return \Magento\Session\SessionManagerInterface
     */
    public function get($instanceName, $data = array())
    {
        $object = $this->_objectManager->get($instanceName, array('data' => $data));
        if (!$object instanceof \Magento\Session\SessionManagerInterface) {
            throw new \LogicException($instanceName . ' doesn\'t implement \Magento\Session\SessionManagerInterface');
        }

        return $object;
    }
}
