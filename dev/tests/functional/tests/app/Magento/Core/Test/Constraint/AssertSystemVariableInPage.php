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
     * Add created variable to page and assert that Custom Variable is displayed on frontend page and has
     * correct data according to dataset.
     *
     * @param SystemVariable $customVariable
     * @param CmsIndex $cmsIndex
     * @param SystemVariable $variable
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @param Store $storeOrigin
     * @param SystemVariable $customVariableOrigin
     * @return void
     */
    public function processAssert(
        SystemVariable $customVariable,
        CmsIndex $cmsIndex,
        SystemVariable $variable,
        FixtureFactory $fixtureFactory,
        Browser $browser,
        Store $storeOrigin = null,
        SystemVariable $customVariableOrigin = null
    ) {
        $cmsPage = $fixtureFactory->createByCode(
            'cmsPage',
            [
                'dataSet' => 'default',
                'data' => ['content' => '{{customVar code=' . $customVariable->getCode() . '}}'],
            ]
        );
        $cmsPage->persist();
        $url = $_ENV['app_frontend_url'] . $cmsPage->getIdentifier();
        $browser->open($url);

        $cmsIndex->getStoreSwitcherBlock()->selectStoreView('Default Store View');

        $htmlValue = ($customVariableOrigin == null)
            ? $customVariable->getHtmlValue()
            : $customVariableOrigin->getHtmlValue();
        $htmlValue = strip_tags($htmlValue);
        $pageContent = $cmsIndex->getMainContentBlock()->getPageContent();
        $this->checkVariable($htmlValue, $pageContent);

        if ($storeOrigin !== null) {
            $cmsIndex->getStoreSwitcherBlock()->selectStoreView($storeOrigin->getName());
            $htmlValue = strip_tags($customVariable->getHtmlValue());
            if ($htmlValue === '') {
                $htmlValue = strip_tags($variable->getHtmlValue());
            }
            $pageContent = $cmsIndex->getMainContentBlock()->getPageContent();
            $this->checkVariable($htmlValue, $pageContent);
        }
    }

    /**
     * Check Variable on frontend page
     *
     * @param string $htmlValue
     * @param string $pageContent
     * @return void
     */
    protected function checkVariable($htmlValue, $pageContent)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $htmlValue,
            $pageContent,
            'Wrong content is displayed on frontend page'
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
        return 'Custom Variable is displayed on frontend page';
    }
}
