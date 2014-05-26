<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block;

use Mtf\Block\Form as BlockForm;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Review form
 *
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
     * Get single product rating
     *
     * @param $code
     * @return Element
     */
    public function getRating($code) {
        return $this->_rootElement->find(sprintf($this->rating, $code), Locator::SELECTOR_XPATH);
    }
}
