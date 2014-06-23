<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Class Edit
 * Review edit form
 */
class Edit extends Form
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
    protected $saveButton = '[data-ui-id$=save-button]';

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
     * Approve review
     *
     * @return void
     */
    public function approveReview()
    {
        $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->setValue('Approved');
        $this->save();
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
}
