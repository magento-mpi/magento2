<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Widget\Test\Fixture\Widget;
use Magento\VersionsCms\Test\Fixture\Version;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceNew;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;

/**
 * Test Creation for New Instance of WidgetEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create cmsHierarchy
 *
 * Steps:
 * 1. Login to the backend
 * 2. Open Content > Frontend Apps
 * 3. Click Add new Widget Instance
 * 4. Fill settings data according dataset
 * 5. Click button Continue
 * 6. Fill widget data according dataset
 * 7. Perform all assertions
 *
 * @group  CMS Content (PS)
 * @ZephyrId MAGETWO-27916
 */
class CreationForNewInstanceOfWidgetEntityTest extends Injectable
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
     */
    public function __inject(
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceNew $widgetInstanceNew,
        WidgetInstanceEdit $widgetInstanceEdit
    ) {
        $this->widgetInstanceIndex = $widgetInstanceIndex;
        $this->widgetInstanceNew = $widgetInstanceNew;
        $this->widgetInstanceEdit = $widgetInstanceEdit;
    }

    /**
     * Creation for New Instance of WidgetEntity
     *
     * @param Widget $widget
     * @param Widget $widgetEdit
     * @return array
     */
    public function test(Widget $widget, Widget $widgetEdit)
    {
        $this->widgetInstanceIndex->open();
        $this->widgetInstanceIndex->getPageActionsBlock()->addNew();
        $this->widgetInstanceNew->getWidgetForm()->fill($widget);
        $this->widgetInstanceNew->getWidgetForm()->clickContinue();

        $this->widgetInstanceEdit->getWidgetForm()->fill($widgetEdit);
        $this->widgetInstanceEdit->getPageActionsBlock()->save();
    }
}
