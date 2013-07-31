<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Local Magento Connect Controller
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Controller_Adminhtml_Extension_Local extends Magento_Adminhtml_Controller_Action
{
    /**
     * Redirect to Magento Connect
     *
     */
    public function indexAction()
    {
        $url = Mage::getBaseUrl('web')
            . 'downloader/?return=' . urlencode(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl());
        $this->getResponse()->setRedirect($url);
    }
}
