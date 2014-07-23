<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml;

use Magento\Review\Test\Fixture\ReviewInjectable;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Form;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Edit
 * Review edit form
 */
class ReviewForm extends Form
{
    /**
     * Posted by field
     *
     * @var string
     */
    protected $customer = '#customer';

    /**
     * Rating status
     *
     * @var string
     */
    protected $status = '[name=status_id]';

    /**
     * 'Save Review' button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id$=save-button-button]';

    /**
     * Selector for single rating
     *
     * @var string
     */
    protected $ratingByNumber = './/*[@id="detailed_rating"]//*[contains(@class,"field-rating")][%d]';

    /**
     * Selector for label of checked rating
     *
     * @var string
     */
    protected $checkedRating = 'input[id$="_%d"]:checked + label';

    /**
     * Rating selector
     *
     * @var string
     */
    protected $rating = './/*[@data-widget="ratingControl"]//label[contains(@for, "%s_%s")]';

    /**
     * Get data from 'Posted By' field
     *
     * @return string
     */
    public function getPostedBy()
    {
        return $this->_rootElement->find($this->customer, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get data from Status field
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->getText();
    }

    /**
     * Set approve review
     *
     * @return void
     */
    public function setApproveReview()
    {
        $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->setValue('Approved');
    }

    /**
     * Get list ratings
     *
     * @return array
     */
    public function getRatings()
    {
        $ratings = [];

        $count = 1;
        $rating = $this->_rootElement->find(sprintf($this->ratingByNumber, $count), Locator::SELECTOR_XPATH);
        while ($rating->isVisible()) {
            $ratings[$count] = [
                'title' => $rating->find('./label/span', Locator::SELECTOR_XPATH)->getText(),
                'rating' => $this->getRatingVote($rating)
            ];

            ++$count;
            $rating = $this->_rootElement->find(sprintf($this->ratingByNumber, $count), Locator::SELECTOR_XPATH);
        }

        return $ratings;
    }

    /**
     * Get rating vote
     *
     * @param Element $rating
     * @return int
     */
    protected function getRatingVote(Element $rating)
    {
        $ratingVote = 5;
        $ratingVoteElement = $rating->find(sprintf($this->checkedRating, $ratingVote));
        while (!$ratingVoteElement->isVisible() && $ratingVote) {
            --$ratingVote;
            $ratingVoteElement = $rating->find(sprintf($this->checkedRating, $ratingVote));
        }

        return $ratingVote;
    }

    /**
     * Fill the review form
     *
     * @param FixtureInterface $review
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $review, Element $element = null)
    {
        parent::fill($review, $element);
        if ($review instanceof ReviewInjectable) {
            $this->fillRatings($review);
        }
    }

    /**
     * Fill ratings on the review form
     *
     * @param ReviewInjectable $review
     * @return void
     */
    protected function fillRatings(ReviewInjectable $review)
    {
        if (!$review->hasData('ratings')) {
            return;
        }

        foreach ($review->getRatings() as $rating) {
            $this->setRating($rating['title'], $rating['rating']);
        }
    }

    /**
     * Set rating vote by rating code
     *
     * @param string $ratingCode
     * @param string $ratingVote
     * @return void
     */
    protected function setRating($ratingCode, $ratingVote)
    {
        $ratingSelector = sprintf($this->rating, $ratingCode, $ratingVote);
        $this->_rootElement->find($ratingSelector, Locator::SELECTOR_XPATH)->click();
    }
}
