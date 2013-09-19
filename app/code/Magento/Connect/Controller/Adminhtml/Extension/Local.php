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
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Backend_Controller_Context $context
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Backend_Controller_Context $context
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Redirect to Magento Connect
     *
     */
    public function indexAction()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl('web')
            . 'downloader/?return=' . urlencode($this->_objectManager->get('Magento_Backend_Helper_Data')->getHomePageUrl());
        $this->getResponse()->setRedirect($url);
    }
}
