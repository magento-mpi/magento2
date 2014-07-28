<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Fixture\Widget;
use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceNew;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;

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
     * @param Widget $widget
     * @param BannerInjectable $banner
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceNew $widgetInstanceNew
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @return void
     */
    public function processAssert(
        Widget $widget,
        BannerInjectable $banner,
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceNew $widgetInstanceNew,
        WidgetInstanceEdit $widgetInstanceEdit
    ) {
        $widgetInstanceIndex->open();
        $widgetInstanceIndex->getPageActionsBlock()->addNew();
        $widgetInstanceNew->getForm()->fill($widget);
        $widgetInstanceNew->getForm()->clickContinue();
        $widgetInstanceEdit->getForm()->openTab('widget_options');

        \PHPUnit_Framework_Assert::assertFalse(
            $widgetInstanceEdit->getBannerGrid()->isRowVisible(['banner' => $banner->getName()]),
            'Banner is present in Widget options grid.'
        );
    }

    /**
     * Banner is absent in Banners grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Banner is absent in Widget options grid.';
    }
}
