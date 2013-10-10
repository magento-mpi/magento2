<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout order review
 */
namespace Magento\Checkout\Block\Onepage\Review;

class Info extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_checkoutSession->getQuote()->getAllVisibleItems();
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->_checkoutSession->getQuote()->getTotals();
    }
}
