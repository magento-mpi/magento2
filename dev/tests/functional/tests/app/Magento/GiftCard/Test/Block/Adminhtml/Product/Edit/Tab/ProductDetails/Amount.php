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
    protected $amount = './/tbody/tr[not(contains(@class,"ignore-validate"))]';

    /**
     * Selector for single amount by number
     *
     * @var string
     */
    protected $amountByNumber = './/tbody/tr[not(contains(@class,"ignore-validate"))][%d]';

    /**
     * Selector for field of amount
     *
     * @var string
     */
    protected $amountDetail = '[name^="product[giftcard_amounts]"][name$="[%s]"]';

    /**
     * Selector for "Add Amount" button
     *
     * @var string
     */
    protected $addAmount = '.action-add';

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

            $amount = $this->find(sprintf($this->amountByNumber, $number), Locator::SELECTOR_XPATH);
            if (!$amount->isVisible()) {
                $this->find($this->addAmount)->click();
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
        $amounts = $this->find($this->amount, Locator::SELECTOR_XPATH)->getElements();
        $value = [];

        foreach ($amounts as $key => $amount) {
            /** @var Element $amount */
            if ($amount->isVisible()) {
                foreach ($this->rowFields as $name) {
                    $value[$key][$name] = $amount->find(sprintf($this->amountDetail, $name))->getValue();
                }
            }
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
        return $this->find(sprintf($this->amountByNumber, $number), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Clear amount rows
     *
     * @return void
     */
    protected function clearAmount()
    {
        $amounts = $this->find($this->amount, Locator::SELECTOR_XPATH)->getElements();
        foreach ($amounts as $amount) {
            /** @var Element $amount */
            if ($amount->isVisible()) {
                $amount->find($this->amountDelete)->click();
            }
        }
    }
}
