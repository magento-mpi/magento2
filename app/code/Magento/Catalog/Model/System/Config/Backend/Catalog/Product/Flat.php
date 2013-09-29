<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat product on/off backend
 */
namespace Magento\Catalog\Model\System\Config\Backend\Catalog\Product;

class Flat extends \Magento\Core\Model\Config\Value
{
    /**
     * After enable flat products required reindex
     *
     * @return \Magento\Catalog\Model\System\Config\Backend\Catalog\Product\Flat
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalog_product_flat')
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
