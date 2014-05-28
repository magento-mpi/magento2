<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint; 

use Magento\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCategorySaveMessage
 * Assert that success message is displayed after category save
 */
class AssertCategorySaveMessage extends AbstractConstraint
{
    /**
     * Success category save message
     */
    const SUCCESS_MESSAGE = 'You saved the category.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after category save
     *
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @return void
     */
    public function processAssert(CatalogCategoryIndex $catalogCategoryIndex)
    {
        $actualMessage = $catalogCategoryIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Success message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message is displayed.';
    }
}
