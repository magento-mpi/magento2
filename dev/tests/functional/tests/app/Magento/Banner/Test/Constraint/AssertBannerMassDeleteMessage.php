<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerMassDeleteMessage
 * Assert that success delete message is appeared after banner has been deleted
 */
class AssertBannerMassDeleteMessage extends AbstractConstraint
{
    /**
     * Message that appears after deletion via mass actions
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted %d record(s).';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success delete message is appeared after banner has been deleted
     *
     * @param BannerInjectable $banner
     * @param BannerIndex|BannerIndex[] $bannerIndex
     * @return void
     */
    public function processAssert(BannerInjectable $banner, BannerIndex $bannerIndex)
    {
        $banners = is_array($banner) ? $banner : [$banner];
        $deleteMessage = sprintf(self::SUCCESS_DELETE_MESSAGE, count($banners));
        \PHPUnit_Framework_Assert::assertEquals(
            $deleteMessage,
            $bannerIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong delete message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Mass delete banner message is displayed.';
    }
}
