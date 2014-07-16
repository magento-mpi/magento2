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
use Mtf\Constraint\AbstractConstraint;
use Magento\Core\Test\Fixture\SystemVariable;

/**
 * Class AssertSystemVariableInPage
 */
class AssertSystemVariableInPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Add created variable to page and assert that Custom Variable is displayed on frontend in page and has
     * correct data according to dataset.
     *
     * @param SystemVariable $customVariable
     * @param CmsIndex $cmsIndex
     * @param SystemVariable $customVariableOrigin
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        SystemVariable $customVariable,
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory,
        Browser $browser,
        SystemVariable $customVariableOrigin = null
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

        if ($cmsIndex->getStoreSwitcherBlock()->isVisible()
            && ($cmsIndex->getStoreSwitcherBlock()->getStoreView() !== 'Default Store View')
        ) {
            $cmsIndex->getStoreSwitcherBlock()->selectStoreView('Default Store View');
        }

        $htmlValue = ($customVariableOrigin == null)
            ? $customVariable->getHtmlValue()
            : $customVariableOrigin->getHtmlValue();
        $htmlValue = strip_tags($htmlValue);

        $pageContent = $cmsIndex->getMainContentBlock()->getPageContent();

        \PHPUnit_Framework_Assert::assertEquals(
            $htmlValue,
            $pageContent,
            'Wrong text is displayed.'
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
        return 'Custom Variable is displayed on frontend page in default storeview.';
    }
}
