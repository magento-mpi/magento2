<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Plugin;

class ShowOutOfStockConfig
{
    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Index\Model\Indexer $indexer
     */
    public function __construct(\Magento\Index\Model\Indexer $indexer)
    {
        $this->_indexer = $indexer;
    }

    /**
     * After save handler
     *
     * @param \Magento\Framework\App\Config\Value $subject
     */
    public function afterSave(\Magento\Framework\App\Config\Value $subject)
    {
        if ($subject->isValueChanged()) {
            $this->_indexer->getProcessByCode('catalog_product_attribute')
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }
    }
}