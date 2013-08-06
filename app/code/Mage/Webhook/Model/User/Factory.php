<?php
/**
 * Creates new instances of Magento_Outbound_UserInterface (via Mage_Webhook_Model_User)
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_User_Factory
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
     * Create a new instance of Mage_Webhook_Model_User
     *
     * @param int $webapiUserId webapi user id
     * @return Mage_Webhook_Model_User
     */
    public function create($webapiUserId)
    {
        return $this->_objectManager->create('Mage_Webhook_Model_User', array('webapiUserId' => $webapiUserId));
    }
}