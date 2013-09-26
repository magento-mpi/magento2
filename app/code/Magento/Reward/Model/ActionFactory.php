<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Model;

class ActionFactory
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
     * Create new action object
     *
     * @param $type
     * @param array $data
     * @return \Magento\Reward\Model\Action\AbstractAction
     */
    public function create($type, array $data = array())
    {
        return $this->_objectManager->create($type, $data);
    }
}
