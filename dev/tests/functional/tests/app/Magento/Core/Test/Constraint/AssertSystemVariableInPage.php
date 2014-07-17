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
     * @param SystemVariable $systemVariable
     * @param CmsIndex $cmsIndex
     * @param \Magento\Core\Test\Fixture\SystemVariable $variable
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @param Store $storeOrigin
     * @param SystemVariable $systemVariableOrigin
     * @return void
     */
    public function processAssert(
        SystemVariable $systemVariable,
        CmsIndex $cmsIndex,
        SystemVariable $variable,
        FixtureFactory $fixtureFactory,
        Browser $browser,
        Store $storeOrigin = null,
        SystemVariable $systemVariableOrigin = null
    ) {

        $content = '{{customVar code=' . $systemVariable->getCode() . '}}';
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

        $cmsIndex->getStoreSwitcherBlock()->selectStoreView('Default Store View');

        $htmlValue = ($systemVariableOrigin == null)
            ? $systemVariable->getHtmlValue()
            : $systemVariableOrigin->getHtmlValue();
        $htmlValue = strip_tags($htmlValue);
        $pageContent = $cmsIndex->getMainContentBlock()->getPageContent();
        $this->checkVariable($htmlValue, $pageContent);

        if ($storeOrigin !== null) {
            $cmsIndex->getStoreSwitcherBlock()->selectStoreView($storeOrigin->getName());
        }
        $htmlValue = strip_tags($systemVariable->getHtmlValue());
        if ($htmlValue === '') {
            $htmlValue = strip_tags($variable->getHtmlValue());
        }
        $pageContent = $cmsIndex->getMainContentBlock()->getPageContent();
        $this->checkVariable($htmlValue, $pageContent);
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
