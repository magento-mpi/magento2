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
     * Rating block selector
     *
     * @var string
     */
    protected $ratingsBlockSelector = '#detailed_rating';

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
        return $this->_rootElement->find(
            $this->ratingsBlockSelector,
            Locator::SELECTOR_CSS,
            'Magento\Review\Test\Block\Adminhtml\Rating\Edit\RatingElement'
        )->getValue();
    }
}
