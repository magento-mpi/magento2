<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml\Rating;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class RatingElement
 * Rating typified element
 */
class RatingElement extends Element
{
    /**
     * Rating selector
     *
     * @var string
     */
    protected $rating = './/*[@data-widget="ratingControl"]//label[contains(@for, "%s_%s")]';

    /**
     * Set rating value
     *
     * @param array|string $value
     * @return void
     */
    public function setValue($value)
    {
        foreach ($value as $rating) {
            $ratingSelector = sprintf($this->rating, $rating['title'], $rating['rating']);
            $this->find($ratingSelector, Locator::SELECTOR_XPATH)->click();
        }
    }
}