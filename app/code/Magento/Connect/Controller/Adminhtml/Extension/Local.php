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
class Magento_Connect_Controller_Adminhtml_Extension_Local extends Magento_Adminhtml_Controller_Action
{
    /**
     * Redirect to Magento Connect
     *
     */
    public function indexAction()
    {
        $url = Mage::getBaseUrl('web')
            . 'downloader/?return=' . urlencode(Mage::helper('Magento_Backend_Helper_Data')->getHomePageUrl());
        $this->getResponse()->setRedirect($url);
    }
}
