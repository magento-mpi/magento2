<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml order create gift message block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create;

class Giftmessage extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
{
    /**
     * @var \Magento\Adminhtml\Model\Giftmessage\Save
     */
    protected $_giftMessageSave;

    /**
     * @param \Magento\Adminhtml\Model\Giftmessage\Save $giftMessageSave
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Adminhtml\Model\Giftmessage\Save $giftMessageSave,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_giftMessageSave = $giftMessageSave;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
    }

    /**
     * Generate form for editing of gift message for entity
     *
     * @param \Magento\Object $entity
     * @param string        $entityType
     * @return string
     */
    public function getFormHtml(\Magento\Object $entity, $entityType='quote') {
        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Sales\Order\Create\Giftmessage\Form')
            ->setEntity($entity)
            ->setEntityType($entityType)
            ->toHtml();
    }

    /**
     * Retrive items allowed for gift messages.
     *
     * If no items available return false.
     *
     * @return array|boolean
     */
    public function getItems()
    {
        $items = array();
        $allItems = $this->getQuote()->getAllItems();

        foreach ($allItems as $item) {
            if($this->_getGiftmessageSaveModel()->getIsAllowedQuoteItem($item)
               && $this->helper('Magento\GiftMessage\Helper\Message')->getIsMessagesAvailable('item',
                        $item, $this->getStore())) {
                // if item allowed
                $items[] = $item;
            }
        }

        if(sizeof($items)) {
            return $items;
        }

        return false;
    }

    /**
     * Retrieve gift message save model
     *
     * @return \Magento\Adminhtml\Model\Giftmessage\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_giftMessageSave;
    }

}
