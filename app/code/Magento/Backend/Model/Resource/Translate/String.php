<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Resource\Translate;

/**
 * Backend string translate resource model
 */
class String extends \Magento\Translation\Model\Resource\String
{
    /**
     * Get current store id
     * Use always default scope for store id
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }
}
