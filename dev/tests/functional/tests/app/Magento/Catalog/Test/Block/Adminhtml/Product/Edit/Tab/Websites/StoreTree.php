<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Websites;

use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element;

/**
 * Class StoreTree
 * Typified element class for store tree element
 */
class StoreTree extends Element
{
    /**
     * Selector for website checkbox
     *
     * @var string
     */
    protected $website = './/*[@class="website-name"]/label[contains(text(),"%s")]/../input';

    /**
     * Selector for selected website checkbox
     *
     * @var string
     */
    protected $selectedWebsite = './/*[@class="website-name"]/input[@checked="checked"][%d]/../label';

    /**
     * Set value
     *
     * @param array|string $values
     * @return void
     * @throws \Exception
     */
    public function setValue($values)
    {
        $values = is_array($values) ? $values : [$values];
        foreach ($values as $value) {
            $website = $this->find(sprintf($this->website, $value), Locator::SELECTOR_XPATH);
            if (!$website->isVisible()) {
                throw new \Exception("Can't find website: \"{$value}\".");
            }
            if (!$website->isSelected()) {
                $website->click();
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
        $values = [];

        $count = 1;
        $website = $this->find(sprintf($this->selectedWebsite, $count), Locator::SELECTOR_XPATH);
        while ($website->isVisible()) {
            $values[] = $website->getText();
            ++$count;
            $website = $this->find(sprintf($this->selectedWebsite, $count), Locator::SELECTOR_XPATH);
        }
        return $values;
    }
}
