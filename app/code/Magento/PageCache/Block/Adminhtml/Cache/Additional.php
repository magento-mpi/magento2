<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System cache management additional block
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Block\Adminhtml\Cache;

class Additional extends \Magento\Adminhtml\Block\Template
{
    /**
     * Get clean cache url
     *
     * @return string
     */
    public function getCleanExternalCacheUrl()
    {
        return $this->getUrl('*/pageCache/clean');
    }

    /**
     * Check if block can be displayed
     *
     * @return bool
     */
    public function canShowButton()
    {
        return \Mage::helper('Magento\PageCache\Helper\Data')->isEnabled()
            && $this->_authorization->isAllowed('Magento_PageCache::page_cache');
    }
}
