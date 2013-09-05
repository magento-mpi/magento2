<?php
/**
 * Creates new Magento_Webhook_Model_Event objects.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event_Factory implements \Magento\PubSub\Event\FactoryInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /** @var \Magento\Convert\Object  */
    private  $_arrayConverter;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Convert\Object $arrayConverter
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Convert\Object $arrayConverter
    ) {
        $this->_objectManager = $objectManager;
        $this->_arrayConverter = $arrayConverter;
    }

    /**
     * Create event
     *
     * @param string $topic Topic on which to publish data
     * @param array $data Data to be published.  Should only contain primitives
     * @return Magento_Webhook_Model_Event
     */
    public function create($topic, $data)
    {
        return $this->_objectManager->create('Magento_Webhook_Model_Event', array(
            'data' => array(
                'topic' => $topic,
                'body_data' => serialize($this->_arrayConverter->convertDataToArray($data))
            )
        ))->setDataChanges(true);
    }

    /**
     * Return the empty instance of Event
     *
     * @return Magento_Webhook_Model_Event
     */
    public function createEmpty()
    {
        return $this->_objectManager->create('Magento_Webhook_Model_Event');
    }
}
