<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search backend model
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Model\Adminhtml\System\Config\Backend;

class Engine extends \Magento\Core\Model\Config\Value
{
    /**
     * After save call
     * Invalidate catalog search index if engine was changed
     *
     * @return \Magento\Search\Model\Adminhtml\System\Config\Backend\Engine
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->isValueChanged()) {
            \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
