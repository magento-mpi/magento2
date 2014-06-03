<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model;

use Magento\Reward\Model\Action\AbstractAction;

class ActionFactory
{
    /**
     * Object manager
     *
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
     * Create new action object
     *
     * @param string $type
     * @param array $data
     * @return AbstractAction
     */
    public function create($type, array $data = array())
    {
        return $this->_objectManager->create($type, $data);
    }
}
