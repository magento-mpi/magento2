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
 * Class CategoryIds
 * Typified element class for category element
 */
class CategoryIds extends MultisuggestElement
{
    /**
     * Selector suggest input
     *
     * @var string
     */
    protected $suggest = '#category_ids-suggest';

    /**
     * Selector item of search result
     *
     * @var string
     */
    protected $resultItem = './/li/a/span[@class="category-label"][text()="%s"]';
}
