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
 * Quote addresses collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_quote_address_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject    = 'quote_address_collection';

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Quote_Address', 'Mage_Sales_Model_Resource_Quote_Address');
    }

    /**
     * Setting filter on quote_id field but if quote_id is 0
     * we should exclude loading junk data from DB
     *
     * @param int $quoteId
     * @return Mage_Sales_Model_Resource_Quote_Address_Collection
     */
    public function setQuoteFilter($quoteId)
    {
        $this->addFieldToFilter('quote_id', $quoteId ? $quoteId : array('null' => 1));
        return $this;
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return Mage_Sales_Model_Resource_Quote_Address_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        Mage::dispatchEvent($this->_eventPrefix.'_load_after', array(
            $this->_eventObject => $this
        ));

        return $this;
    }
}

