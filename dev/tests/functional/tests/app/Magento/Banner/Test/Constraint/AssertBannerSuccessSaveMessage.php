<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerSuccessSaveMessage
 * Assert that after banner save "You saved the banner." successful message appears
 */
class AssertBannerSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'You saved the banner.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after banner save "You saved the banner." successful message appears
     *
     * @param BannerIndex $bannerIndex
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(BannerIndex $bannerIndex, AdminCache $adminCache)
    {
        $actualMessage = $bannerIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $actualMessage
        );

        $adminCache->open();
        $adminCache->getActionsBlock()->flushCacheStorage();
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
