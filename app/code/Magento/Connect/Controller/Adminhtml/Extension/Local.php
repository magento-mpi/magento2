<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Local Magento Connect Controller
 *
 * @category    Magento
 * @package     Magento_Connect
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
        $url = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getBaseUrl('web')
            . 'downloader/?return=' . urlencode($this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl());
        $this->getResponse()->setRedirect($url);
    }
}
