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
namespace Magento\Sales\Model\Quote\Address\Total;

class Nominal extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Invoke collector for nominal items
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param \Magento\Sales\Model\Quote\Address\Total\Nominal
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        $collector = \Mage::getModel('\Magento\Sales\Model\Quote\Address\Total\Nominal\Collector',
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
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return \Magento\Sales\Model\Quote\Address\Total\Nominal
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
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
