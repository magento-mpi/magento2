<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Block\Sales\Order;

class Giftcards extends \Magento\Framework\View\Element\Template
{
    /**
     * Gift card account data
     *
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $_giftCardAccountData = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        array $data = []
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
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

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Retrieve gift cards applied to current order
     *
     * @return array
     */
    public function getGiftCards()
    {
        $result = [];
        $source = $this->getSource();
        if (!$source instanceof \Magento\Sales\Model\Order) {
            return $result;
        }
        $cards = $this->_giftCardAccountData->getCards($this->getOrder());
        foreach ($cards as $card) {
            $obj = new \Magento\Framework\Object();
            $obj->setBaseAmount($card['ba'])->setAmount($card['a'])->setCode($card['c']);

            $result[] = $obj;
        }
        return $result;
    }

    /**
     * Initialize giftcard order total
     *
     * @return $this
     */
    public function initTotals()
    {
        $total = new \Magento\Framework\Object(
            [
                'code' => $this->getNameInLayout(),
                'block_name' => $this->getNameInLayout(),
                'area' => $this->getArea(),
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, ['customerbalance', 'grand_total']);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return mixed
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
