<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Widget\Test\Fixture\Widget;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceNew;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;

/**
 * Test Creation for Delete Widget Entity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Widget
 *
 * Steps:
 * 1. Login to backend
 * 2. Open Content > Frontend Apps
 * 3. Open Widget from preconditions
 * 4. Delete
 * 5. Perform all asserts
 *
 * @group  Widget_(PS)
 * @ZephyrId MAGETWO-28459
 */
class DeleteWidgetEntityTest extends Injectable
{
    /**
     * WidgetInstanceIndex page
     *
     * @var WidgetInstanceIndex
     */
    protected $widgetInstanceIndex;

    /**
     * WidgetInstanceNew page
     *
     * @var WidgetInstanceNew
     */
    protected $widgetInstanceNew;

    /**
     * WidgetInstanceEdit page
     *
     * @var WidgetInstanceEdit
     */
    protected $widgetInstanceEdit;

    /**
     * Injection data
     *
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceNew $widgetInstanceNew
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceNew $widgetInstanceNew,
        WidgetInstanceEdit $widgetInstanceEdit,
        FixtureFactory $fixtureFactory
    ) {
        $this->widgetInstanceIndex = $widgetInstanceIndex;
        $this->widgetInstanceNew = $widgetInstanceNew;
        $this->widgetInstanceEdit = $widgetInstanceEdit;

        $cmsPage = $fixtureFactory->createByCode('cmsPage', ['dataSet' => 'default']);
        $cmsPage->persist();

        $widget = $fixtureFactory->createByCode(
            'widget',
            [
                'dataSet' => 'cms_page_link',
                'data' => [
                    'parameters' => [
                        'page_id' => $cmsPage->getPageId(),
                    ],
                ]
            ]
        );
        $widget->persist();

        return ['widget' => $widget];
    }

    /**
     * Delete Widget Entity test
     *
     * @param Widget $widget
     * @return void
     */
    public function test(Widget $widget)
    {
        $filter = ['title' => $widget->getTitle()];
        $this->widgetInstanceIndex->open();
        $this->widgetInstanceIndex->getWidgetGrid()->searchAndOpen($filter);
        $this->widgetInstanceEdit->getPageActionsBlock()->delete();
    }
}
