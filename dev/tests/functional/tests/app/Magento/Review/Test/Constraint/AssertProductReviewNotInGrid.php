<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductReviewNotInGrid
 */
class AssertProductReviewNotInGrid extends AbstractConstraint
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
    public $filter = [
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
     * Asserts Product Review not available in grid
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewInjectable $review
     * @param string $gridStatus
     * @param CatalogProductSimple $product
     * @param ReviewInjectable $reviewInitial
     * @return void
     */
    public function processAssert(
        ReviewIndex $reviewIndex,
        ReviewInjectable $review,
        $gridStatus = '',
        $product = null,
        ReviewInjectable $reviewInitial = null
    ) {
        if ($product === null) {
            $product = $reviewInitial === null
                ? $review->getDataFieldConfig('entity_id')['source']->getEntity()
                : $reviewInitial->getDataFieldConfig('entity_id')['source']->getEntity();
        }
        $filter = $this->prepareFilter($product, $review, $gridStatus);

        $reviewIndex->getReviewGrid()->search($filter);
        unset($filter['visible_in']);
        \PHPUnit_Framework_Assert::assertFalse(
            $reviewIndex->getReviewGrid()->isRowVisible($filter, false),
            'Review available in grid'
        );
    }

    /**
     * Prepare filter for assert
     *
     * @param FixtureInterface $product
     * @param ReviewInjectable $review
     * @param string $gridStatus
     * @return array
     */
    public function prepareFilter(FixtureInterface $product, ReviewInjectable $review, $gridStatus)
    {
        $filter = [];
        foreach ($this->filter as $key => $item) {
            list($type, $param) = [$key, $item];
            if (is_numeric($key)) {
                $type = $param = $item;
            }
            switch ($param) {
                case 'name':
                case 'sku':
                    $value = $product->getData($param);
                    break;
                case 'select_stores':
                    $value = $review->getData($param)[0];
                    break;
                case 'status_id':
                    $value = $gridStatus != '' ? $gridStatus : $review->getData($param);
                    break;
                default:
                    $value = $review->getData($param);
                    break;
            }
            if ($value !== null) {
                $filter += [$type => $value];
            }
        }
        return $filter;
    }

    /**
     * Text success if review not in grid on product reviews tab
     *
     * @return string
     */
    public function toString()
    {
        return 'Review is absent in grid on product reviews tab.';
    }
}
