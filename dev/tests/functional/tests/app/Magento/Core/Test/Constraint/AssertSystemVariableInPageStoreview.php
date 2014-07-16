<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Store\Test\Fixture\Store;
use Mtf\Constraint\AbstractConstraint;
use Magento\Core\Test\Fixture\SystemVariable;

/**
 * Class AssertSystemVariableInPageStoreview
 * Assert that Custom Variable is displayed on frontend page and has correct data according to dataset
 */
class AssertSystemVariableInPageStoreview extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created custom variable is displayed on frontend in non default storeview and has
     * correct data according to dataset.
     *
     * @param SystemVariable $customVariable
     * @param CmsIndex $cmsIndex
     * @param SystemVariable $variable
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @param Store $storeOrigin\
     * @return void
     */
    public function processAssert(
        SystemVariable $customVariable,
        CmsIndex $cmsIndex,
        SystemVariable $variable,
        FixtureFactory $fixtureFactory,
        Browser $browser,
        Store $storeOrigin = null
    ) {

        $content = '{{customVar code=' . $customVariable->getCode() . '}}';
        $cmsPage = $fixtureFactory->createByCode(
            'cmsPage',
            [
                'dataSet' => 'default',
                'data' => ['content' => $content],
            ]
        );
        $cmsPage->persist();
        $url = $_ENV['app_frontend_url'] . $cmsPage->getIdentifier();
        $browser->open($url);

        if ($storeOrigin !== null) {
            $cmsIndex->getStoreSwitcherBlock()->selectStoreView($storeOrigin->getName());
        }
        $htmlValue = strip_tags($customVariable->getHtmlValue());
        if ($htmlValue === '') {
            $htmlValue = strip_tags($variable->getHtmlValue());
        }
        $pageContent = $cmsIndex->getMainContentBlock()->getPageContent();

        \PHPUnit_Framework_Assert::assertEquals(
            $htmlValue,
            $pageContent,
            'Wrong success message is displayed.'
            . "\nExpected: " . $htmlValue
            . "\nActual: " . $pageContent
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Custom Variable is displayed on frontend page.';
    }
}
