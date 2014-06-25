<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model\Sales\Pdf;

class Weee extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    /**
     * Check if weee total amount should be included
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $weeeTotal = 0;

        /** @var $items array of \Magento\Sales\Model\Order\Item */
        $items = $this->getSource()->getAllItems();
        foreach ($items as $item) {
            $weeeTotal += $item->getWeeeTaxAppliedRowAmount();
        }

        // If we have no Weee, check if we still need to display it
        if (!$weeeTotal && !filter_var($this->getDisplayZero(), FILTER_VALIDATE_BOOLEAN)) {
            return array();
        }

        // Display the Weee total amount
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = array(
            array(
                'amount' => $this->getOrder()->formatPriceTxt($weeeTotal),
                'label' => __($this->getTitle()) . ':',
                'font_size' => $fontSize
            )
        );

        return $totals;
    }
}
