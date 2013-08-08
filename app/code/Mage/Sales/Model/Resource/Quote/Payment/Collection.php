<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote payments collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Payment_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Quote_Payment', 'Mage_Sales_Model_Resource_Quote_Payment');
    }

    /**
     * Setquote filter to result
     *
     * @param int $quoteId
     * @return Mage_Sales_Model_Resource_Quote_Payment_Collection
     */
    public function setQuoteFilter($quoteId)
    {
        return $this->addFieldToFilter('quote_id', $quoteId);
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        /** @var Mage_Sales_Model_Quote_Payment $item */
        foreach ($this->_items as $item) {
            foreach ($item->getData() as $fieldName => $fieldValue) {
                $item->setData($fieldName,
                    Mage::getSingleton('Mage_Sales_Model_Payment_Method_Converter')->decode($item, $fieldName)
                );
            }
        }

        return parent::_afterLoad();
    }
}

