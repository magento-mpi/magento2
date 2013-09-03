<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Quote_Address_Total extends \Magento\Object
{
    /**
     * Merge numeric total values
     *
     * @param Magento_Sales_Model_Quote_Address_Total $total
     * @return Magento_Sales_Model_Quote_Address_Total
     */
    public function merge(Magento_Sales_Model_Quote_Address_Total $total)
    {
        $newData = $total->getData();
        foreach ($newData as $key => $value) {
            if (is_numeric($value)) {
                $this->setData($key, $this->_getData($key)+$value);
            }
        }
        return $this;
    }
}
