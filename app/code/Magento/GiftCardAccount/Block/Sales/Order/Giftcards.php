<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Sales\Order;

class Giftcards extends \Magento\Core\Block\Template
{
    /**
     * Gift card account data
     *
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $_giftCardAccountData = null;

    /**
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Retreive gift cards applied to current order
     *
     * @return array
     */
    public function getGiftCards()
    {
        $result = array();
        $source = $this->getSource();
        if (!($source instanceof \Magento\Sales\Model\Order)) {
            return $result;
        }
        $cards = $this->_giftCardAccountData->getCards($this->getOrder());
        foreach ($cards as $card) {
            $obj = new \Magento\Object();
            $obj->setBaseAmount($card['ba'])
                ->setAmount($card['a'])
                ->setCode($card['c']);

            $result[] = $obj;
        }
        return $result;
    }

    /**
     * Initialize giftcard order total
     *
     * @return \Magento\GiftCardAccount\Block\Sales\Order\Giftcards
     */
    public function initTotals()
    {
        $total = new \Magento\Object(array(
            'code'      => $this->getNameInLayout(),
            'block_name'=> $this->getNameInLayout(),
            'area'      => $this->getArea()
        ));
        $this->getParentBlock()->addTotalBefore($total, array('customerbalance', 'grand_total'));
        return $this;
    }

    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
