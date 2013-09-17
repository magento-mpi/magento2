<?php
/**
 * Creates new instances of Magento_Outbound_UserInterface (via Magento_Webhook_Model_User)
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_User_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Magento_Webhook_Model_User
     *
     * @param int $webapiUserId webapi user id
     * @return Magento_Webhook_Model_User
     */
    public function create($webapiUserId)
    {
        return $this->_objectManager->create('Magento_Webhook_Model_User', array('webapiUserId' => $webapiUserId));
    }
}
