<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Dashboard;

class Tunnel extends \Magento\Backend\Controller\Adminhtml\Dashboard
{
    /**
     * Forward request for a graph image to the web-service
     *
     * This is done in order to include the image to a HTTPS-page regardless of web-service settings
     *
     * @return void
     */
    public function execute()
    {
        $error = __('invalid request');
        $httpCode = 400;
        $gaData = $this->_request->getParam('ga');
        $gaHash = $this->_request->getParam('h');
        if ($gaData && $gaHash) {
            /** @var $helper \Magento\Backend\Helper\Dashboard\Data */
            $helper = $this->_objectManager->get('Magento\Backend\Helper\Dashboard\Data');
            $newHash = $helper->getChartDataHash($gaData);
            if ($newHash == $gaHash) {
                $params = json_decode(base64_decode(urldecode($gaData)), true);
                if ($params) {
                    try {
                        /** @var $httpClient \Magento\Framework\HTTP\ZendClient */
                        $httpClient = $this->_objectManager->create('Magento\Framework\HTTP\ZendClient');
                        $response = $httpClient->setUri(
                            \Magento\Backend\Block\Dashboard\Graph::API_URL
                        )->setParameterGet(
                            $params
                        )->setConfig(
                            array('timeout' => 5)
                        )->request(
                            'GET'
                        );

                        $headers = $response->getHeaders();

                        $this->_response->setHeader(
                            'Content-type',
                            $headers['Content-type']
                        )->setBody(
                            $response->getBody()
                        );
                        return;
                    } catch (\Exception $e) {
                        $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                        $error = __('see error log for details');
                        $httpCode = 503;
                    }
                }
            }
        }
        $this->_response->setBody(
            __('Service unavailable: %1', $error)
        )->setHeader(
            'Content-Type',
            'text/plain; charset=UTF-8'
        )->setHttpResponseCode(
            $httpCode
        );
    }
}
