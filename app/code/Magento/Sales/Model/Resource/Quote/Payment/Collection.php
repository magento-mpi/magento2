<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quote payments collection
 */
namespace Magento\Sales\Model\Resource\Quote\Payment;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Sales\Model\Payment\Method\Converter
     */
    protected $_converter;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Sales\Model\Payment\Method\Converter $converter
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Sales\Model\Payment\Method\Converter $converter,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $this->_converter = $converter;
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Quote\Payment', 'Magento\Sales\Model\Resource\Quote\Payment');
    }

    /**
     * Setquote filter to result
     *
     * @param int $quoteId
     * @return \Magento\Sales\Model\Resource\Quote\Payment\Collection
     */
    public function setQuoteFilter($quoteId)
    {
        return $this->addFieldToFilter('quote_id', $quoteId);
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        /** @var \Magento\Sales\Model\Quote\Payment $item */
        foreach ($this->_items as $item) {
            foreach ($item->getData() as $fieldName => $fieldValue) {
                $item->setData($fieldName, $this->_converter->decode($item, $fieldName));
            }
        }

        return parent::_afterLoad();
    }
}

