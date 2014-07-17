<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class MultisuggestElement
 * Typified element class for multi suggest element
 */
class MultisuggestElement extends SuggestElement
{
    /**
     * Selector list choice
     *
     * @var string
     */
    protected $listChoice = './/ul[contains(@class,"mage-suggest-choices")]';

    /**
     * Selector choice item
     *
     * @var string
     */
    protected $choice = './/li/div[text()="%s"]/..';

    /**
     * Selector choice value
     *
     * @var string
     */
    protected $choiceValue = './/li[contains(@class,"mage-suggest-choice")]/div';

    /**
     * Selector remove choice item
     *
     * @var string
     */
    protected $choiceClose = '.mage-suggest-choice-close';

    /**
     * Set value
     *
     * @param array|string $values
     * @return void
     */
    public function setValue($values)
    {
        $values = is_array($values) ? $values : [$values];
        $this->clear();
        foreach ($values as $value) {
            if (!$this->isChoice($value)) {
                parent::setValue($value);
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
        $listChoice = $this->find($this->listChoice, Locator::SELECTOR_XPATH);
        $choices = $listChoice->find($this->choiceValue, Locator::SELECTOR_XPATH)->getElements();
        $values = [];

        foreach ($choices as $choice) {
            /** @var Element $choice */
            $values[] = $choice->getText();
        }
        return $values;
    }

    /**
     * Check exist selected item
     *
     * @param string $value
     * @return bool
     */
    protected function isChoice($value)
    {
        return $this->find(sprintf($this->choice, $value), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Clear element
     *
     * @return void
     */
    protected function clear()
    {
        $choiceClose = $this->find($this->choiceClose);
        while ($choiceClose->isVisible()) {
            $choiceClose->click();
            $choiceClose = $this->find($this->choiceClose);
        }
    }
}
