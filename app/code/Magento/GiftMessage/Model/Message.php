<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message model
 *
 * @method \Magento\GiftMessage\Model\Resource\Message _getResource()
 * @method \Magento\GiftMessage\Model\Resource\Message getResource()
 * @method int getCustomerId()
 * @method \Magento\GiftMessage\Model\Message setCustomerId(int $value)
 * @method string getSender()
 * @method \Magento\GiftMessage\Model\Message setSender(string $value)
 * @method string getRecipient()
 * @method \Magento\GiftMessage\Model\Message setRecipient(string $value)
 * @method string getMessage()
 * @method \Magento\GiftMessage\Model\Message setMessage(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Model;

class Message extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\GiftMessage\Model\TypeFactory
     */
    protected $_typeFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\GiftMessage\Model\Resource\Message $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param \Magento\GiftMessage\Model\TypeFactory $typeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\GiftMessage\Model\Resource\Message $resource,
        \Magento\Data\Collection\Db $resourceCollection,
        \Magento\GiftMessage\Model\TypeFactory $typeFactory,
        array $data = array()
    ) {
        $this->_typeFactory = $typeFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento\GiftMessage\Model\Resource\Message');
    }

    /**
     * Return model from entity type
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getEntityModelByType($type)
    {
        return $this->_typeFactory->createType($type);
    }

    /**
     * Checks thats gift message is empty
     *
     * @return boolean
     */
    public function isMessageEmpty()
    {
        return trim($this->getMessage()) == '';
    }
}
