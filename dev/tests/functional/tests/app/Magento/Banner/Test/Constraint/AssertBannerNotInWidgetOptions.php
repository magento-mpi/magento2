<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceNew;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;

/**
 * Class AssertBannerNotInWidgetOptions
 * Check that deleted banner is absent in Widget options bunnerGrid and can't be found by name
 */
class AssertBannerNotInWidgetOptions extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted banner is absent in Widget options bunnerGrid and can't be found by name
     *
     * @param BannerInjectable $banner
     * @param FixtureFactory $fixtureFactory
     * @param WidgetInstanceNew $widgetInstanceNew
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @return void
     */
    public function processAssert(
        BannerInjectable $banner,
        FixtureFactory $fixtureFactory,
        WidgetInstanceNew $widgetInstanceNew,
        WidgetInstanceEdit $widgetInstanceEdit
    ) {
        $widget = $fixtureFactory->create(
            '\Magento\Banner\Test\Fixture\Widget',
            ['dataSet' => 'widget_banner_rotator']
        );
        $widgetInstanceNew->open();
        $widgetInstanceNew->getWidgetForm()->fill($widget);
        $widgetInstanceEdit->getWidgetForm()->openTab('widget_options');

        \PHPUnit_Framework_Assert::assertFalse(
            $widgetInstanceEdit->getBannerGrid()->isRowVisible(['banner' => $banner->getName()]),
            'Banner is present on Widget Options tab in Banner grid.'
        );
    }

    /**
     * Banner is absent in Banners grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Banner is absent on Widget Options tab in Banner grid.';
    }
}
