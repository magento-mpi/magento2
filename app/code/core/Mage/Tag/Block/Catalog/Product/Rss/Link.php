<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product rss link builder class
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Block_Catalog_Product_Rss_Link extends Mage_Core_Block_Template
{
    /**
     * Keep true in cases when rss feed enabled for tagged products
     *
     * @var bool
     */
    protected $_isRssEnabled;

    /**
     * Id of tag
     *
     * @var int
     */
    protected $_tagId;

    /**
     * @var Mage_Tag_Model_Tag
     */
    protected $_tagModel;

    /**
     * @var Mage_Core_Model_Url
     */
    protected $_coreUrlModel;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );

        if (isset($data['rss_catalog_tag_enabled'])) {
            $this->_isRssEnabled = $data['rss_catalog_tag_enabled'];
        } else {
            $this->_isRssEnabled = $this->_storeConfig->getConfig('rss/catalog/tag');
        }

        if (isset($data['tag_id'])) {
            $this->_tagId = $data['tag_id'];
        } else {
            $this->_tagId = $this->getRequest()->getParam('tagId');
        }

        if (isset($data['tag_model'])) {
            $this->_tagModel = $data['tag_model'];
        } else {
            $this->_tagModel = Mage::getModel('Mage_Tag_Model_Tag');
        }

        if (isset($data['core_url_model'])) {
            $this->_coreUrlModel = $data['core_url_model'];
        } else {
            $this->_coreUrlModel = Mage::getModel('Mage_Core_Model_Url');
        }
    }


    /**
     * Retrieve link on product rss feed tagged with loaded tag
     *
     * @return bool|string
     */
    public function getLinkUrl()
    {
        if ($this->_isRssEnabled && $this->_tagId) {
            /** @var $tagModel Mage_Tag_Model_Tag */
            $this->_tagModel->load($this->_tagId);
            if ($this->_tagModel && $this->_tagModel->getId()) {
                return $this->_coreUrlModel->getUrl('rss/catalog/tag',
                    array('tagName' => urlencode($this->_tagModel->getName()))
                );
            }
        }

        return false;
    }
}
