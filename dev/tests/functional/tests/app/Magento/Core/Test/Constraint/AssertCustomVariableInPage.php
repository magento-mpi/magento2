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
 * Class AssertCustomVariableInPage
 */
class AssertCustomVariableInPage extends AbstractConstraint
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
                'data' => [
                    'content' => [
                        'content' => '{{customVar code=' . $customVariable->getCode() . '}}'
                    ]
                ],
            ]
        );
        $cmsPage->persist();
        $browser->open($_ENV['app_frontend_url'] . $cmsPage->getIdentifier());
        $cmsIndex->getStoreSwitcherBlock()->selectStoreView('Default Store View');

        $data = $customVariableOrigin
            ? array_replace_recursive($customVariableOrigin->getData(), $customVariable->getData())
            : $customVariable->getData();
        $fixtureContent = empty($data['html_value']) ? $data['plain_value'] : strip_tags($data['html_value']);
        $pageContent = $cmsIndex->getCmsPageBlock()->getPageContent();
        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureContent,
            $pageContent,
            'Wrong content is displayed on frontend page'
            . "\nExpected: " . $fixtureContent
            . "\nActual: " . $pageContent
        );

        if ($storeOrigin !== null) {
            $cmsIndex->getStoreSwitcherBlock()->selectStoreView($storeOrigin->getName());
            $pageContent = $cmsIndex->getCmsPageBlock()->getPageContent();
            \PHPUnit_Framework_Assert::assertEquals(
                $fixtureContent,
                $pageContent,
                'Wrong content is displayed on frontend page'
                . "\nExpected: " . $fixtureContent
                . "\nActual: " . $pageContent
            );
        }
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
