<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductForm;

/**
 * Class AssertGiftCardProductForm
 */
class AssertGiftCardProductForm extends AssertProductForm
{
    /**
     * Sort fields for fixture and form data
     *
     * @var array
     */
    protected $sortFields = [
        'giftcard_amounts::price'
    ];

    /* tags */
     const SEVERITY = 'middle';
     /* end tags */
}
