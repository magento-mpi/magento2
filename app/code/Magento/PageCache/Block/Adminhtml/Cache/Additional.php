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
     * @var Magento_PageCache_Helper_Data
     */
    protected $_pageCacheData = null;

    /**
     * @param Magento_PageCache_Helper_Data $pageCacheData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_PageCache_Helper_Data $pageCacheData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
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
