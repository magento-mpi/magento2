<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer api
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Api extends Magento_Customer_Model_Api_Resource
{
    protected $_mapAttributes = array(
        'customer_id' => 'entity_id'
    );
    /**
     * Prepare data to insert/update.
     * Creating array for stdClass Object
     *
     * @param stdClass $data
     * @return array
     */
    /**
     * Api data
     *
     * @var Magento_Api_Helper_Data
     */
    protected $_apiData = null;

    /**
     * @param Magento_Api_Helper_Data $apiData
     */
    public function __construct(
        Magento_Api_Helper_Data $apiData
    ) {
        $this->_apiData = $apiData;
    }

    protected function _prepareData($data)
    {
       foreach ($this->_mapAttributes as $attributeAlias=>$attributeCode) {
            if(isset($data[$attributeAlias]))
            {
                $data[$attributeCode] = $data[$attributeAlias];
                unset($data[$attributeAlias]);
            }
        }
        return $data;
    }

    /**
     * Create new customer
     *
     * @param array $customerData
     * @return int
     */
    public function create($customerData)
    {
        $customerData = $this->_prepareData($customerData);
        try {
            $customer = Mage::getModel('Magento_Customer_Model_Customer')
                ->setData($customerData)
                ->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $customer->getId();
    }

    /**
     * Retrieve customer data
     *
     * @param int $customerId
     * @param array $attributes
     * @return array
     */
    public function info($customerId, $attributes = null)
    {
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($customerId);

        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }

        if (!is_null($attributes) && !is_array($attributes)) {
            $attributes = array($attributes);
        }

        $result = array();

        foreach ($this->_mapAttributes as $attributeAlias=>$attributeCode) {
            $result[$attributeAlias] = $customer->getData($attributeCode);
        }

        foreach ($this->getAllowedAttributes($customer, $attributes) as $attributeCode=>$attribute) {
            $result[$attributeCode] = $customer->getData($attributeCode);
        }

        return $result;
    }

    /**
     * Retrieve customers data
     *
     * @param  object|array $filters
     * @return array
     */
    public function items($filters)
    {
        $collection = Mage::getModel('Magento_Customer_Model_Customer')->getCollection()->addAttributeToSelect('*');
        /** @var $apiHelper Magento_Api_Helper_Data */
        $apiHelper = $this->_apiData;
        $filters = $apiHelper->parseFilters($filters, $this->_mapAttributes);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Magento_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $customer) {
            $data = $customer->toArray();
            $row  = array();
            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = (isset($data[$attributeCode]) ? $data[$attributeCode] : null);
            }
            foreach ($this->getAllowedAttributes($customer) as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Update customer data
     *
     * @param int $customerId
     * @param array $customerData
     * @return boolean
     */
    public function update($customerId, $customerData)
    {
        $customerData = $this->_prepareData($customerData);

        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($customerId);

        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }

        foreach ($this->getAllowedAttributes($customer) as $attributeCode=>$attribute) {
            if (isset($customerData[$attributeCode])) {
                $customer->setData($attributeCode, $customerData[$attributeCode]);
            }
        }

        $customer->save();
        return true;
    }

    /**
     * Delete customer
     *
     * @param int $customerId
     * @return boolean
     */
    public function delete($customerId)
    {
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($customerId);

        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $customer->delete();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }

} // Class Magento_Customer_Model_Customer_Api End
