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

class Additional extends \Magento\Backend\Block\Template
{
    /**
     * Page cache data
     *
     * @var \Magento\PageCache\Helper\Data
     */
    protected $_pageCacheData = null;

    /**
     * @param \Magento\PageCache\Helper\Data $pageCacheData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\PageCache\Helper\Data $pageCacheData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_pageCacheData = $pageCacheData;
        parent::__construct($coreData, $context, $data);
    }

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
        return $this->_pageCacheData->isEnabled()
            && $this->_authorization->isAllowed('Magento_PageCache::page_cache');
    }
}
