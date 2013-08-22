<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product rss link builder class
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Block_Catalog_Product_Rss_Link extends Magento_Core_Block_Template
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
     * @var Magento_Tag_Model_Tag
     */
    protected $_tagModel;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_coreUrlModel;

    /**
     * Initialize object
     */
    protected function _construct()
    {
        if ($this->hasData('rss_catalog_tag_enabled')) {
            $this->_isRssEnabled = $this->getData('rss_catalog_tag_enabled');
        } else {
            $this->_isRssEnabled = $this->_storeConfig->getConfig('rss/catalog/tag');
        }

        if ($this->hasData('tag_id')) {
            $this->_tagId = $this->getData('tag_id');
        } else {
            $this->_tagId = $this->getRequest()->getParam('tagId');
        }

        if ($this->hasData('tag_model')) {
            $this->_tagModel = $this->getData('tag_model');
        } else {
            $this->_tagModel = Mage::getModel('Magento_Tag_Model_Tag');
        }

        if ($this->hasData('core_url_model')) {
            $this->_coreUrlModel = $this->getData('core_url_model');
        } else {
            $this->_coreUrlModel = Mage::getModel('Magento_Core_Model_Url');
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
            /** @var $tagModel Magento_Tag_Model_Tag */
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
