<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Custom Options Observer
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Option;

class Observer
{
    /**
     * Copy quote custom option files to order custom option files
     *
     * @param \Magento\Object $observer
     * @return \Magento\Catalog\Model\Product\Option\Observer
     */
    public function copyQuoteFilesToOrderFiles($observer)
    {
        /* @var $quoteItem \Magento\Sales\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getItem();

        if (is_array($quoteItem->getOptions())) {
            foreach ($quoteItem->getOptions() as $itemOption) {
                $code = explode('_', $itemOption->getCode());
                if (isset($code[1]) && is_numeric($code[1]) && ($option = $quoteItem->getProduct()->getOptionById($code[1]))) {
                    if ($option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FILE) {
                        /* @var $_option \Magento\Catalog\Model\Product\Option */
                        try {
                            $group = $option->groupFactory($option->getType())
                                ->setQuoteItemOption($itemOption)
                                ->copyQuoteToOrder();

                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }
        }
        return $this;
    }
}
