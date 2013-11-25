<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorizenet Backend Data Helper
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Helper;

class Backend extends Data
{
    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Backend\Model\Url $urlBuilder
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Backend\Model\Url $urlBuilder
    ) {
        parent::__construct($context, $storeManager, $orderFactory);
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Return URL for admin area
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _getUrl($route, $params = array())
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * Retrieve place order url in admin
     *
     * @return  string
     */
    public function getPlaceOrderAdminUrl()
    {
        return $this->_getUrl('*/authorizenet_directpost_payment/place', array());
    }

    /**
     * Retrieve place order url
     *
     * @param array params
     * @return  string
     */
    public function getSuccessOrderUrl($params)
    {
        $param = array();
        $route = 'sales/order/view';
        $order = $this->_orderFactory->create()->loadByIncrementId($params['x_invoice_num']);
        $param['order_id'] = $order->getId();
        return $this->_getUrl($route, $param);
    }

    /**
     * Retrieve redirect ifrmae url
     *
     * @param array params
     * @return string
     */
    public function getRedirectIframeUrl($params)
    {
        return $this->_getUrl('adminhtml/authorizenet_directpost_payment/redirect', $params);
    }
}
