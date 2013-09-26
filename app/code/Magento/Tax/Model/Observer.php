<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Event Observer
 */
class Magento_Tax_Model_Observer
{
    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData;

    /**
     * @var Magento_Tax_Model_Sales_Order_TaxFactory
     */
    protected $_orderTaxFactory;

    /**
     * @var Magento_Tax_Model_Sales_Order_Tax_ItemFactory
     */
    protected $_taxItemFactory;

    /**
     * @var Magento_Tax_Model_Calculation
     */
    protected $_calculation;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Tax_Model_Resource_Report_TaxFactory
     */
    protected $_reportTaxFactory;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Tax_Model_Sales_Order_TaxFactory $orderTaxFactory
     * @param Magento_Tax_Model_Sales_Order_Tax_ItemFactory $taxItemFactory
     * @param Magento_Tax_Model_Calculation $calculation
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Tax_Model_Resource_Report_TaxFactory $reportTaxFactory
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Tax_Model_Sales_Order_TaxFactory $orderTaxFactory,
        Magento_Tax_Model_Sales_Order_Tax_ItemFactory $taxItemFactory,
        Magento_Tax_Model_Calculation $calculation,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Tax_Model_Resource_Report_TaxFactory $reportTaxFactory
    ) {
        $this->_taxData = $taxData;
        $this->_orderTaxFactory = $orderTaxFactory;
        $this->_taxItemFactory = $taxItemFactory;
        $this->_calculation = $calculation;
        $this->_locale = $locale;
        $this->_reportTaxFactory = $reportTaxFactory;
    }

    /**
     * Put quote address tax information into order
     *
     * @param Magento_Event_Observer $observer
     */
    public function salesEventConvertQuoteAddressToOrder(Magento_Event_Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        $order = $observer->getEvent()->getOrder();

        $taxes = $address->getAppliedTaxes();
        if (is_array($taxes)) {
            if (is_array($order->getAppliedTaxes())) {
                $taxes = array_merge($order->getAppliedTaxes(), $taxes);
            }
            $order->setAppliedTaxes($taxes);
            $order->setConvertingFromQuote(true);
        }
    }

    /**
     * Save order tax information
     *
     * @param Magento_Event_Observer $observer
     */
    public function salesEventOrderAfterSave(Magento_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order->getConvertingFromQuote() || $order->getAppliedTaxIsSaved()) {
            return;
        }

        $getTaxesForItems   = $order->getQuote()->getTaxesForItems();
        $taxes              = $order->getAppliedTaxes();

        $ratesIdQuoteItemId = array();
        if (!is_array($getTaxesForItems)) {
            $getTaxesForItems = array();
        }
        foreach ($getTaxesForItems as $quoteItemId => $taxesArray) {
            foreach ($taxesArray as $rates) {
                if (count($rates['rates']) == 1) {
                    $ratesIdQuoteItemId[$rates['id']][] = array(
                        'id'        => $quoteItemId,
                        'percent'   => $rates['percent'],
                        'code'      => $rates['rates'][0]['code']
                    );
                } else {
                    $percentDelta   = $rates['percent'];
                    $percentSum     = 0;
                    foreach ($rates['rates'] as $rate) {
                        $ratesIdQuoteItemId[$rates['id']][] = array(
                            'id'        => $quoteItemId,
                            'percent'   => $rate['percent'],
                            'code'      => $rate['code']
                        );
                        $percentSum += $rate['percent'];
                    }

                    if ($percentDelta != $percentSum) {
                        $delta = $percentDelta - $percentSum;
                        foreach ($ratesIdQuoteItemId[$rates['id']] as &$rateTax) {
                            if ($rateTax['id'] == $quoteItemId) {
                                $rateTax['percent'] = (($rateTax['percent'] / $percentSum) * $delta)
                                        + $rateTax['percent'];
                            }
                        }
                    }
                }
            }
        }

        foreach ($taxes as $id => $row) {
            foreach ($row['rates'] as $tax) {
                if (is_null($row['percent'])) {
                    $baseRealAmount = $row['base_amount'];
                } else {
                    if ($row['percent'] == 0 || $tax['percent'] == 0) {
                        continue;
                    }
                    $baseRealAmount = $row['base_amount'] / $row['percent'] * $tax['percent'];
                }
                $hidden = (isset($row['hidden']) ? $row['hidden'] : 0);
                $data = array(
                    'order_id'          => $order->getId(),
                    'code'              => $tax['code'],
                    'title'             => $tax['title'],
                    'hidden'            => $hidden,
                    'percent'           => $tax['percent'],
                    'priority'          => $tax['priority'],
                    'position'          => $tax['position'],
                    'amount'            => $row['amount'],
                    'base_amount'       => $row['base_amount'],
                    'process'           => $row['process'],
                    'base_real_amount'  => $baseRealAmount,
                );

                /** @var $orderTax Magento_Tax_Model_Sales_Order_Tax */
                $orderTax = $this->_orderTaxFactory->create();
                $result = $orderTax->setData($data)->save();

                if (isset($ratesIdQuoteItemId[$id])) {
                    foreach ($ratesIdQuoteItemId[$id] as $quoteItemId) {
                        if ($quoteItemId['code'] == $tax['code']) {
                            $item = $order->getItemByQuoteItemId($quoteItemId['id']);
                            if ($item) {
                                $data = array(
                                    'item_id'       => $item->getId(),
                                    'tax_id'        => $result->getTaxId(),
                                    'tax_percent'   => $quoteItemId['percent']
                                );
                                /** @var $taxItem Magento_Tax_Model_Sales_Order_Tax_Item */
                                $taxItem = $this->_taxItemFactory->create();
                                $taxItem->setData($data)->save();
                            }
                        }
                    }
                }
            }
        }

        $order->setAppliedTaxIsSaved(true);
    }

    /**
     * Add tax percent values to product collection items
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Tax_Model_Observer
     */
    public function addTaxPercentToProductCollection($observer)
    {
        $helper = $this->_taxData;
        $collection = $observer->getEvent()->getCollection();
        $store = $collection->getStoreId();
        if (!$helper->needPriceConversion($store)) {
            return $this;
        }

        if ($collection->requireTaxPercent()) {
            $request = $this->_calculation->getRateRequest();
            foreach ($collection as $item) {
                if (null === $item->getTaxClassId()) {
                    $item->setTaxClassId($item->getMinimalTaxClassId());
                }
                if (!isset($classToRate[$item->getTaxClassId()])) {
                    $request->setProductClassId($item->getTaxClassId());
                    $classToRate[$item->getTaxClassId()] = $this->_calculation->getRate($request);
                }
                $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
            }

        }
        return $this;
    }

    /**
     * Refresh sales tax report statistics for last day
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_Tax_Model_Observer
     */
    public function aggregateSalesReportTaxData($schedule)
    {
        $this->_locale->emulate(0);
        $currentDate = $this->_locale->date();
        $date = $currentDate->subHour(25);
        /** @var $reportTax Magento_Tax_Model_Resource_Report_Tax */
        $reportTax = $this->_reportTaxFactory->create();
        $reportTax->aggregate($date);
        $this->_locale->revert();
        return $this;
    }

    /**
     * Reset extra tax amounts on quote addresses before recollecting totals
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Tax_Model_Observer
     */
    public function quoteCollectTotalsBefore(Magento_Event_Observer $observer)
    {
        /* @var $quote Magento_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        foreach ($quote->getAllAddresses() as $address) {
            $address->setExtraTaxAmount(0);
            $address->setBaseExtraTaxAmount(0);
        }
        return $this;
    }
}
