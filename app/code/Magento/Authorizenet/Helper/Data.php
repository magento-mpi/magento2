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
 * Authorizenet Data Helper
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Authorizenet_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * @var Magento_Core_Model_App
     */
    protected $_application;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Backend_Model_Url
     */
    protected $_urlBuilder;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_App $application
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Backend_Model_Url $urlBuilder
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_App $application,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Backend_Model_Url $urlBuilder
    ) {
        parent::__construct($context);
        $this->_application = $application;
        $this->_storeManager = $storeManager;
        $this->_orderFactory = $orderFactory;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Return URL for admin area
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getAdminUrl($route, $params)
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * Set secure url checkout is secure for current store.
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    protected function _getUrl($route, $params = array())
    {
        $params['_type'] = Magento_Core_Model_Store::URL_TYPE_LINK;
        if (isset($params['is_secure'])) {
            $params['_secure'] = (bool)$params['is_secure'];
        } elseif ($this->_storeManager->getStore()->isCurrentlySecure()) {
            $params['_secure'] = true;
        }
        return parent::_getUrl($route, $params);
    }

    /**
     * Retrieve save order url params
     *
     * @param string $controller
     * @return array
     */
    public function getSaveOrderUrlParams($controller)
    {
        $route = array();
        switch ($controller) {
            case 'onepage':
                $route['action'] = 'saveOrder';
                $route['controller'] = 'onepage';
                $route['module'] = 'checkout';
                break;

            case 'sales_order_create':
            case 'sales_order_edit':
                $route['action'] = 'save';
                $route['controller'] = 'sales_order_create';
                $route['module'] = 'admin';
                break;

            default:
                break;
        }

        return $route;
    }

    /**
     * Retrieve redirect ifrmae url
     *
     * @param array params
     * @return string
     */
    public function getRedirectIframeUrl($params)
    {
        switch ($params['controller_action_name']) {
            case 'onepage':
                $route = 'authorizenet/directpost_payment/redirect';
                break;

            case 'sales_order_create':
            case 'sales_order_edit':
                $route = 'adminhtml/authorizenet_directpost_payment/redirect';
                return $this->getAdminUrl($route, $params);

            default:
                $route = 'authorizenet/directpost_payment/redirect';
                break;
        }

        return $this->_getUrl($route, $params);
    }

    /**
     * Retrieve place order url on front
     *
     * @return  string
     */
    public function getPlaceOrderFrontUrl()
    {
        return $this->_getUrl('authorizenet/directpost_payment/place');
    }

    /**
     * Retrieve place order url in admin
     *
     * @return  string
     */
    public function getPlaceOrderAdminUrl()
    {
        return $this->getAdminUrl('*/authorizenet_directpost_payment/place', array());
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
        switch ($params['controller_action_name']) {
            case 'onepage':
                $route = 'checkout/onepage/success';
                break;

            case 'sales_order_create':
            case 'sales_order_edit':
                $route = 'adminhtml/sales_order/view';
                $order = $this->_orderFactory->create()->loadByIncrementId($params['x_invoice_num']);
                $param['order_id'] = $order->getId();
                return $this->getAdminUrl($route, $param);

            default :
                $route = 'checkout/onepage/success';
                break;
        }

        return $this->_getUrl($route, $param);
    }

    /**
     * Get controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->_application->getFrontController()->getRequest()->getControllerName();
    }

    /**
     * Update all child and parent order's edit increment numbers.
     * Needed for Admin area.
     *
     * @param Magento_Sales_Model_Order $order
     */
    public function updateOrderEditIncrements(Magento_Sales_Model_Order $order)
    {
        if ($order->getId() && $order->getOriginalIncrementId()) {
            $collection = $order->getCollection();
            $quotedIncrId = $collection->getConnection()->quote($order->getOriginalIncrementId());
            $collection->getSelect()->where(
                "original_increment_id = {$quotedIncrId} OR increment_id = {$quotedIncrId}"
            );

            foreach ($collection as $orderToUpdate) {
                $orderToUpdate->setEditIncrement($order->getEditIncrement());
                $orderToUpdate->save();
            }
        }
    }
}
