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
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Payment;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
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
                $item->setData($fieldName,
                    \Mage::getSingleton('Magento\Sales\Model\Payment\Method\Converter')->decode($item, $fieldName)
                );
            }
        }

        return parent::_afterLoad();
    }
}

