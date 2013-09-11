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
 * Flat category on/off backend
 */
namespace Magento\Catalog\Model\System\Config\Backend\Catalog\Category;

class Flat extends \Magento\Core\Model\Config\Value
{
    /**
     * After enable flat category required reindex
     *
     * @return \Magento\Catalog\Model\System\Config\Backend\Catalog\Category\Flat
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            \Mage::getModel('Magento\Index\Model\Indexer')
                ->getProcessByCode(\Magento\Catalog\Helper\Category\Flat::CATALOG_CATEGORY_FLAT_PROCESS_CODE)
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
