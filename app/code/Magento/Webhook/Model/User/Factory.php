<?php
/**
 * Creates new instances of \Magento\Outbound\UserInterface (via \Magento\Webhook\Model\User)
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\User;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of \Magento\Webhook\Model\User
     *
     * @param int $webapiUserId webapi user id
     * @return \Magento\Webhook\Model\User
     */
    public function create($webapiUserId)
    {
        return $this->_objectManager->create('Magento\Webhook\Model\User', array('webapiUserId' => $webapiUserId));
    }
}
