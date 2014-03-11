<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

class Website
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_processor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
    ) {
        $this->_processor = $processor;
    }

    /**
     * Invalidate price indexer
     *
     * @param \Magento\Core\Model\Resource\Website $subject
     * @param \Magento\Core\Model\Resource\Website $result
     * @return \Magento\Core\Model\Resource\Website
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(\Magento\Core\Model\Resource\Website $subject, $result)
    {
        $this->_processor->markIndexerAsInvalid();
        return $result;
    }
}
