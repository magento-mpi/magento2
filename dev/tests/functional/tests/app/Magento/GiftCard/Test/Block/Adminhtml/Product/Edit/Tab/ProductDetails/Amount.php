<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Block\Adminhtml\Product\Edit\Tab\ProductDetails;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element as AbstractElement;

/**
 * Class Amount
 * Typified element class for amount block
 */
class Amount extends AbstractElement
{
    /**
     * Mapping for field of single amount
     *
     * @var array
     */
    protected $rowFields = ['price'];

    /**
     * Selector for single amount
     *
     * @var string
     */
    protected $amount = './/tbody/tr[not(contains(@class,"ignore-validate"))][%d]';

    /**
     * Selector for field of amount
     *
     * @var string
     */
    protected $amountDetail = '[name^="product[giftcard_amounts]"][name$="[%s]"]';

    /**
     * Selector delete button of amount
     *
     * @var string
     */
    protected $amountDelete = 'button.action-delete';

    /**
     * Set value
     *
     * @param array $values
     * @return void
     * @throws \Exception
     */
    public function setValue($values)
    {
        if (!is_array($values)) {
            throw new \Exception('Values must be array.');
        }

        $this->clearAmount();
        foreach ($values as $number => $amountData) {
            /* Throw exception if isn't exist previous amount. */
            if (1 < $number && !$this->isAmountVisible($number - 1)) {
                throw new \Exception("Invalid argument: can't fill amount #{$number}");
            }

            $amount = $this->find(sprintf($this->amount, $number), Locator::SELECTOR_XPATH);
            if (!$amount->isVisible()) {
                $this->find('.action-add')->click();
            }
            foreach ($this->rowFields as $name) {
                if (isset($amountData[$name])) {
                    $amount->find(sprintf($this->amountDetail, $name))->setValue($amountData[$name]);
                }
            }
        }
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getValue()
    {
        $value = [];

        $count = 1;
        $amount = $this->find(sprintf($this->amount, $count), Locator::SELECTOR_XPATH);
        while ($amount->isVisible()) {
            foreach ($this->rowFields as $name) {
                $value[$count][$name] = $amount->find(sprintf($this->amountDetail, $name))->getValue();
            }
            ++$count;
            $amount = $this->find(sprintf($this->amount, $count), Locator::SELECTOR_XPATH);
        }

        return $value;
    }

    /**
     * Check visible amount by number
     *
     * @param int $number
     * @return bool
     */
    protected function isAmountVisible($number)
    {
        return $this->find(sprintf($this->amount, $number), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Clear amount rows
     *
     * @return void
     */
    protected function clearAmount()
    {
        $selector = sprintf($this->amount, 1);
        $amount = $this->find($selector, Locator::SELECTOR_XPATH);
        while ($amount->isVisible()) {
            $amount->find($this->amountDelete)->click();
            $amount = $this->find($selector, Locator::SELECTOR_XPATH);
        }
    }
}
