<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Resource;

/**
 * Backend translate resource model
 */
class Translate extends \Magento\Core\Model\Resource\Translate
{
    /**
     * Get current store id
     * Use always default scope for store id
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return \Magento\Core\Model\Store::DEFAULT_STORE_ID;
    }
}
