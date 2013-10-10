<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Checkout\Block\Onepage;

class Failure extends \Magento\Core\Block\Template
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

    public function getRealOrderId()
    {
        return $this->_checkoutSession->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = $this->_checkoutSession->getErrorMessage();
        return $error;
    }

    /**
     * Continue shopping URL
     *
     *  @return	  string
     */
    public function getContinueShoppingUrl()
    {
        return $this->getUrl('checkout/cart');
    }
}
