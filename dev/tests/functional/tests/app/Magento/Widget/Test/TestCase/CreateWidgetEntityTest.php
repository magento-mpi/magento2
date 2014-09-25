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
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceNew;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;

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
 * @group  Widget_(PS)
 * @ZephyrId MAGETWO-27916
 */
class CreateWidgetEntityTest extends Injectable
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
     * Widget fixture
     *
     * @var Widget
     */
    protected $widget;

    /**
     * Page CatalogRuleIndex
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndex;

    /**
     * Page CatalogRuleNew
     *
     * @var CatalogRuleNew
     */
    protected $catalogRuleNew;

    /**
     * Page PromoQuoteEdit
     *
     * @var PromoQuoteEdit
     */
    protected $promoQuoteEdit;

    /**
     * Page PromoQuoteIndex
     *
     * @var PromoQuoteIndex
     */
    protected $promoQuoteIndex;

    /**
     * Injection data
     *
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceNew $widgetInstanceNew
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleNew $catalogRuleNew
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @return void
     */
    public function __inject(
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceNew $widgetInstanceNew,
        WidgetInstanceEdit $widgetInstanceEdit,
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleNew $catalogRuleNew,
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit
    ) {
        $this->widgetInstanceIndex = $widgetInstanceIndex;
        $this->widgetInstanceNew = $widgetInstanceNew;
        $this->widgetInstanceEdit = $widgetInstanceEdit;
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleNew = $catalogRuleNew;
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
    }

    /**
     * Creation for New Instance of WidgetEntity
     *
     * @param Widget $widget
     * @return void
     */
    public function test(Widget $widget)
    {
        $this->widget = $widget;
        $this->widgetInstanceIndex->open();
        $this->widgetInstanceIndex->getPageActionsBlock()->addNew();
        $this->widgetInstanceNew->getWidgetForm()->fill($widget);
        $this->widgetInstanceEdit->getPageActionsBlock()->save();
    }

    /**
     * Removing widget, catalog rules and sales rules
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->widget !== null) {
            $filter = ['title' => $this->widget->getTitle()];
            $this->widgetInstanceIndex->open();
            $this->widgetInstanceIndex->getWidgetGrid()->searchAndOpen($filter);
            $this->widgetInstanceEdit->getPageActionsBlock()->delete();

            if (isset($this->widget->getWidgetOptions()[0]['entities']['banner_catalog_rules'])) {
                $filterCatalogPriceRule = [
                    'rule_id' => $this->widget->getWidgetOptions()[0]['entities']['banner_catalog_rules'],
                ];
                $this->catalogRuleIndex->open();
                $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filterCatalogPriceRule);
                $this->catalogRuleNew->getFormPageActions()->delete();
            }
            if (isset($this->widget->getWidgetOptions()[0]['entities']['banner_sales_rules'])) {
                $filter = [
                    'rule_id' => $this->widget->getWidgetOptions()[0]['entities']['banner_sales_rules'],
                ];
                $this->promoQuoteIndex->open();
                $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen($filter);
                $this->promoQuoteEdit->getFormPageActions()->delete();
            }
        }
    }
}
