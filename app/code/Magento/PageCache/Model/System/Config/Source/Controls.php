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
class Magento_PageCache_Model_System_Config_Source_Controls implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Page cache data
     *
     * @var Magento_PageCache_Helper_Data
     */
    protected $_pageCacheData = null;

    /**
     * @param Magento_PageCache_Helper_Data $pageCacheData
     */
    public function __construct(
        Magento_PageCache_Helper_Data $pageCacheData
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
