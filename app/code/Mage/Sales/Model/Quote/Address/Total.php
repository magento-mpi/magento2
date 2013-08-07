<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sales_Model_Quote_Address_Total extends Magento_Object
{
    /**
     * Merge numeric total values
     *
     * @param Mage_Sales_Model_Quote_Address_Total $total
     * @return Mage_Sales_Model_Quote_Address_Total
     */
    public function merge(Mage_Sales_Model_Quote_Address_Total $total)
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
