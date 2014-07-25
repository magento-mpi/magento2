<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Local;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Redirect to Magento Connect
     *
     * @return void
     */
    public function execute()
    {
        $url = $this->_objectManager->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getStore()->getBaseUrl(
            'web'
        ) . 'downloader/?return=' . urlencode(
            $this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl()
        );
        $this->getResponse()->setRedirect($url);
    }
}
