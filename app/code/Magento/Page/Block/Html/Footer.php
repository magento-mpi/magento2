<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html page block
 *
 * @category   Magento
 * @package    Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Html_Footer extends Magento_Core_Block_Template
{

    protected $_copyright;

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'=> false,
            'cache_tags'    => array(Magento_Core_Model_Store::CACHE_TAG, Magento_Cms_Model_Block::CACHE_TAG)
        ));
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'PAGE_FOOTER',
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId(),
            Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()
        );
    }

    public function setCopyright($copyright)
    {
        $this->_copyright = $copyright;
        return $this;
    }

    public function getCopyright()
    {
        if (!$this->_copyright) {
            $this->_copyright = Mage::getStoreConfig('design/footer/copyright');
        }

        return $this->_copyright;
    }
}
