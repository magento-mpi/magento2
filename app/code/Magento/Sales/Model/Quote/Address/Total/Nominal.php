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
 * Nominal items total
 * Collects only items segregated by isNominal property
 * Aggregates row totals per item
 */
class Magento_Sales_Model_Quote_Address_Total_Nominal extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Invoke collector for nominal items
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @param Magento_Sales_Model_Quote_Address_Total_Nominal
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        $collector = Mage::getModel('Magento_Sales_Model_Quote_Address_Total_Nominal_Collector',
            array('store' => $address->getQuote()->getStore())
        );

        // invoke nominal totals
        foreach ($collector->getCollectors() as $model) {
            $model->collect($address);
        }

        // aggregate collected amounts into one to have sort of grand total per item
        foreach ($address->getAllNominalItems() as $item) {
            $rowTotal = 0; $baseRowTotal = 0;
            $totalDetails = array();
            foreach ($collector->getCollectors() as $model) {
                $itemRowTotal = $model->getItemRowTotal($item);
                if ($model->getIsItemRowTotalCompoundable($item)) {
                    $rowTotal += $itemRowTotal;
                    $baseRowTotal += $model->getItemBaseRowTotal($item);
                    $isCompounded = true;
                } else {
                    $isCompounded = false;
                }
                if ((float)$itemRowTotal > 0 && $label = $model->getLabel()) {
                    $totalDetails[] = new \Magento\Object(array(
                        'label'  => $label,
                        'amount' => $itemRowTotal,
                        'is_compounded' => $isCompounded,
                    ));
                }
            }
            $item->setNominalRowTotal($rowTotal);
            $item->setBaseNominalRowTotal($baseRowTotal);
            $item->setNominalTotalDetails($totalDetails);
        }

        return $this;
    }

    /**
     * Fetch collected nominal items
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Quote_Address_Total_Nominal
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        $items = $address->getAllNominalItems();
        if ($items) {
            $address->addTotal(array(
                'code'    => $this->getCode(),
                'title'   => __('Subscription Items'),
                'items'   => $items,
                'area'    => 'footer',
            ));
        }
        return $this;
    }
}
