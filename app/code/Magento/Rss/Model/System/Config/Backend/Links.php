<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
namespace Magento\Rss\Model\System\Config\Backend;

class Links extends \Magento\Core\Model\Config\Value
{
    /**
     * Invalidate cache type, when value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            /** @var \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList */
            $cacheTypeList = \Mage::getObjectManager()->get('Magento\Core\Model\Cache\TypeListInterface');
            $cacheTypeList->invalidate(\Magento\Core\Block\AbstractBlock::CACHE_GROUP);
        }
    }

}
