<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductEdit;

/**
 * Class AssertProductSaveMessage
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the product.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after product save
     *
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(CatalogProductEdit $productPage)
    {
        $actualMessage = $productPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Product success save message is present.';
    }
}
