<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\ProductDetails;

use Mtf\Client\Driver\Selenium\Element\MultisuggestElement;

/**
 * Class ParentCategoryIds
 * Typified element class for parent category element
 */
class ParentCategoryIds extends MultisuggestElement
{
    /**
     * Selector suggest input
     *
     * @var string
     */
    protected $suggest = '#new_category_parent-suggest';

    /**
     * Selector item of search result
     *
     * @var string
     */
    protected $resultItem = './/li/a/span[@class="category-label"][text()="%s"]';
}
