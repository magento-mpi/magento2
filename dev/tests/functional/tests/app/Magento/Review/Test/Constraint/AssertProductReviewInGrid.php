<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductReviewInGrid
 */
class AssertProductReviewInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Filter params
     *
     * @var array
     */
    protected $filter = [
        'review_id',
        'status' => 'status_id',
        'title',
        'nickname',
        'detail',
        'visible_in' => 'select_stores',
        'type',
        'name',
        'sku'
    ];

    /**
     * Assert that review is displayed in grid
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewInjectable $review
     * @param string $status [optional]
     * @return void
     */
    public function processAssert(ReviewIndex $reviewIndex, ReviewInjectable $review, $status = '')
    {
        $filter = [];
        foreach ($this->filter as $key => $item) {
            list($type, $param) = [$key, $item];
            if (is_numeric($key)) {
                $type = $param = $item;
            }
            switch ($param) {
                case 'name' :
                case 'sku' :
                    $value = $review->getDataFieldConfig('entity_id')['source']->getEntity()->getData($param);
                    break;
                case 'select_stores' :
                    $value = $review->getData($param)[0];
                    break;
                case 'status_id' :
                    $value = $status != '' ? $status : $review->getData($param);
                    break;
                default :
                    $value = $review->getData($param);
                    break;
            }
            if ($value !== null) {
                $filter += [$type => $value];
            }
        }

        $reviewIndex->open();
        $reviewIndex->getReviewGrid()->search($filter);
        unset($filter['visible_in']);
        \PHPUnit_Framework_Assert::assertTrue(
            $reviewIndex->getReviewGrid()->isRowVisible($filter, false),
            'Review with is absent in Review grid.'
        );
    }

    /**
     * Text success exist review in grid on product reviews tab
     *
     * @return string
     */
    public function toString()
    {
        return 'Review is present in grid on product reviews tab.';
    }
}
