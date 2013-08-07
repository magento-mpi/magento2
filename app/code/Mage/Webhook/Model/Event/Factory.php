<?php
/**
 * Creates new Mage_Webhook_Model_Event objects.
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Event_Factory implements Magento_PubSub_Event_FactoryInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Magento_Convert_Object  */
    private  $_arrayConverter;

    /**
     * Initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Convert_Object $arrayConverter
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Convert_Object $arrayConverter
    ) {
        $this->_objectManager = $objectManager;
        $this->_arrayConverter = $arrayConverter;
    }

    /**
     * Create event
     *
     * @param string $topic Topic on which to publish data
     * @param array $data Data to be published.  Should only contain primitives
     * @return Mage_Webhook_Model_Event
     */
    public function create($topic, $data)
    {
        return $this->_objectManager->create('Mage_Webhook_Model_Event', array(
            'data' => array(
                'topic' => $topic,
                'body_data' => serialize($this->_arrayConverter->convertDataToArray($data)),
                'status' => Magento_PubSub_EventInterface::READY_TO_SEND
            )
        ))->setDataChanges(true);
    }

    /**
     * Return the empty instance of Event
     *
     * @return Mage_Webhook_Model_Event
     */
    public function createEmpty()
    {
        return $this->_objectManager->create('Mage_Webhook_Model_Event');
    }
}
