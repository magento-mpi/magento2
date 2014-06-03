<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Local Magento Connect Controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Controller\Adminhtml\Extension;

class Local extends \Magento\Backend\App\Action
{
    /**
     * Redirect to Magento Connect
     *
     * @return void
     */
    public function indexAction()
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
