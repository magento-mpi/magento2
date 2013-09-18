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
namespace Magento\Page\Block\Html;

class Footer extends \Magento\Core\Block\Template
{

    protected $_copyright;

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'=> false,
            'cache_tags'    => array(\Magento\Core\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG)
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
            \Mage::app()->getStore()->getId(),
            (int)\Mage::app()->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId(),
            \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()
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
            $this->_copyright = $this->_storeConfig->getConfig('design/footer/copyright');
        }

        return $this->_copyright;
    }
}
