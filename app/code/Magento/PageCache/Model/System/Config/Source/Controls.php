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
 * Page cache system config source model
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Model\System\Config\Source;

class Controls implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Page cache data
     *
     * @var \Magento\PageCache\Helper\Data
     */
    protected $_pageCacheData = null;

    /**
     * @param \Magento\PageCache\Helper\Data $pageCacheData
     */
    public function __construct(
        \Magento\PageCache\Helper\Data $pageCacheData
    ) {
        $this->_pageCacheData = $pageCacheData;
    }

    /**
     * Return array of external cache controls for using as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_pageCacheData->getCacheControls() as $code => $type) {
            $options[] = array(
                'value' => $code,
                'label' => $type['label']
            );
        }
        return $options;
    }
}
