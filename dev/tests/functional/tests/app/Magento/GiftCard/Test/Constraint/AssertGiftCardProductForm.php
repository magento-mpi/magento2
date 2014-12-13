<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCard\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductForm;

/**
 * Class AssertGiftCardProductForm
 */
class AssertGiftCardProductForm extends AssertProductForm
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Sort fields for fixture and form data
     *
     * @var array
     */
    protected $sortFields = [
        'giftcard_amounts::price',
    ];
}
