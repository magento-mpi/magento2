<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable checkout success page
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Checkout;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return true if order(s) has one or more downloadable products
     *
     * @return bool
     */
    public function getOrderHasDownloadable()
    {
        $hasDownloadableFlag = $this->_checkoutSession->getHasDownloadableProducts(true);
        if (!$this->isOrderVisible()) {
            return false;
        }
        /**
         * if use guest checkout
         */
        if (!$this->_customerSession->getCustomerId()) {
            return false;
        }
        return $hasDownloadableFlag;
    }

    /**
     * Return url to list of ordered downloadable products of customer
     *
     * @return string
     */
    public function getDownloadableProductsUrl()
    {
        return $this->getUrl('downloadable/customer/products', array('_secure' => true));
    }
}
