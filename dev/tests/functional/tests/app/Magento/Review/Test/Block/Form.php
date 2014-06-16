<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block;

use Magento\Review\Test\Fixture\Rating;
use Mtf\Block\Form as BlockForm;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Review form
 */
class Form extends BlockForm
{
    /**
     * Legend selector
     *
     * @var string
     */
    protected $legendSelector = 'legend';

    /**
     * 'Submit' review button selector
     *
     * @var string
     */
    protected $submitButton = '.action.submit';

    /**
     * Single product rating selector
     *
     * @var string
     */
    protected $rating = './/*[@id="%s_rating_label"]/..[contains(@class,"rating")]';

    /**
     * Submit review form
     */
    public function submit()
    {
        $this->_rootElement->find($this->submitButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Get legend
     *
     * @return Element
     */
    public function getLegend()
    {
        return $this->_rootElement->find($this->legendSelector);
    }

    /**
     * Check rating element is visible
     *
     * @param Rating $rating
     * @return bool
     */
    public function isVisibleRating(Rating $rating)
    {
        return $this->getRating($rating)->isVisible();
    }

    /**
     * Get single product rating
     *
     * @param Rating $rating
     * @return Element
     */
    protected function getRating(Rating $rating)
    {
        return $this->_rootElement->find(sprintf($this->rating, $rating->getRatingCode()), Locator::SELECTOR_XPATH);
    }
}
