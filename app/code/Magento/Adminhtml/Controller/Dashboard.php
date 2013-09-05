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
 * Dashboard admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Dashboard extends Magento_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(__('Dashboard'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Adminhtml::dashboard');
        $this->_addBreadcrumb(__('Dashboard'), __('Dashboard'));
        $this->renderLayout();
    }

    /**
     * Gets most viewed products list
     *
     */
    public function productsViewedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Gets latest customers list
     *
     */
    public function customersNewestAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Gets the list of most active customers
     *
     */
    public function customersMostAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxBlockAction()
    {
        $output   = '';
        $blockTab = $this->getRequest()->getParam('block');
        $blockClassSuffix = str_replace(' ', '_', ucwords(str_replace('_', ' ', $blockTab)));

        if (in_array($blockTab, array('tab_orders', 'tab_amounts', 'totals'))) {
            $output = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Dashboard_' . $blockClassSuffix)->toHtml();
        }
        $this->getResponse()->setBody($output);
        return;
    }

    /**
     * Forward request for a graph image to the web-service
     *
     * This is done in order to include the image to a HTTPS-page regardless of web-service settings
     */
    public function tunnelAction()
    {
        $error = __('invalid request');
        $httpCode = 400;
        $gaData = $this->_request->getParam('ga');
        $gaHash = $this->_request->getParam('h');
        if ($gaData && $gaHash) {
            /** @var $helper Magento_Adminhtml_Helper_Dashboard_Data */
            $helper = $this->_objectManager->get('Magento_Adminhtml_Helper_Dashboard_Data');
            $newHash = $helper->getChartDataHash($gaData);
            if ($newHash == $gaHash) {
                $params = json_decode(base64_decode(urldecode($gaData)), true);
                if ($params) {
                    try {
                        /** @var $httpClient \Magento\HTTP\ZendClient */
                        $httpClient = $this->_objectManager->create('Magento\HTTP\ZendClient');
                        $response = $httpClient->setUri(Magento_Adminhtml_Block_Dashboard_Graph::API_URL)
                            ->setParameterGet($params)
                            ->setConfig(array('timeout' => 5))
                            ->request('GET');

                        $headers = $response->getHeaders();

                        $this->_response->setHeader('Content-type', $headers['Content-type'])
                            ->setBody($response->getBody());
                        return;
                    } catch (Exception $e) {
                        $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
                        $error = __('see error log for details');
                        $httpCode = 503;
                    }
                }
            }
        }
        $this->_response->setBody(__('Service unavailable: %1', $error))
            ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setHttpResponseCode($httpCode);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::dashboard');
    }
}
