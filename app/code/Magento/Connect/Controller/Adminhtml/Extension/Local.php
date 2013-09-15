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

class Local extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Redirect to Magento Connect
     *
     */
    public function indexAction()
    {
        $url = Mage::getBaseUrl('web')
            . 'downloader/?return=' . urlencode($this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl());
        $this->getResponse()->setRedirect($url);
    }
}
