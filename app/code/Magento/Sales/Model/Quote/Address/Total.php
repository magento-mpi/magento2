<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sales\Model\Quote\Address;

class Total extends \Magento\Object
{
    /**
     * Merge numeric total values
     *
     * @param \Magento\Sales\Model\Quote\Address\Total $total
     * @return \Magento\Sales\Model\Quote\Address\Total
     */
    public function merge(\Magento\Sales\Model\Quote\Address\Total $total)
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
