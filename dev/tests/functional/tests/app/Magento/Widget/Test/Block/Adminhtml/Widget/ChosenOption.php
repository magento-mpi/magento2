<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Widget Chosen Option
 */
class ChosenOption extends Element
{
    /**
     * Select page button selector
     *
     * @var string
     */
    protected $selectButton = '//ancestor::body//button[contains(@class,"btn-chooser")]';

    /**
     * Magento varienLoader.js loader
     *
     * @var string
     */
    protected $loaderOld = '//ancestor::body/div[@id="loading-mask"]';

    // @codingStandardsIgnoreStart
    /**
     * Select block selector
     *
     * @var string
     */
    protected $selectBlock = "//ancestor::body//div[contains(@style,'display: block')]//*[contains(@id,'responseCntoptions')]";
    // @codingStandardsIgnoreEnd

    /**
     * Page widget chooser block class
     *
     * @var string
     */
    protected $pageWidgetChooserBlockClass = 'Magento\Cms\Test\Block\Adminhtml\Page\Widget\Chooser';

    /**
     * Category widget chooser block class
     *
     * @var string
     */
    protected $categoryWidgetChooserBlockClass = '\Magento\Catalog\Test\Block\Adminhtml\Category\Widget\Chooser';

    /**
     * Product widget chooser block class
     *
     * @var string
     */
    protected $productWidgetChooserBlockClass = '\Magento\Catalog\Test\Block\Adminhtml\Product\Widget\Chooser';

    /**
     * Entity chooser block class mapping
     *
     * @var array
     */
    protected $chooserClasses = [
        'page' => 'Magento\Cms\Test\Block\Adminhtml\Page\Widget\Chooser',
        'category' => 'Magento\Catalog\Test\Block\Adminhtml\Category\Widget\Chooser',
        'product' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Widget\Chooser'
    ];

    /**
     * Select widget options
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->clickSelectButton();
        if (isset($value['filter_url_key'])) {
            $this->getClassBlock($this->chooserClasses['page'])
                ->searchAndOpen(['chooser_identifier' => $value['filter_url_key']]);
        }
        if (isset($value['filter_identifier'])) {
            $this->getClassBlock($this->chooserClasses['page'])
                ->searchAndOpen(['chooser_identifier' => $value['filter_identifier']]);
        }
        if (isset($value['category_path'])) {
            if (isset($value['filter_sku'])) {
                $this->getClassBlock($this->chooserClasses['category'])
                    ->selectCategoryByName($value['category_path']);
                $this->getClassBlock($this->chooserClasses['product'])
                    ->searchAndOpen(['chooser_sku' => $value['filter_sku']]);
            } else {
                $this->getClassBlock($this->chooserClasses['category'])
                    ->selectCategoryByName($value['category_path']);
            }
        }
    }

    /**
     * Clicking to select button
     *
     * @return void
     */
    protected function clickSelectButton()
    {
        $this->find($this->selectButton, Locator::SELECTOR_XPATH)->click();
        $this->waitLoader();
    }

    /**
     * Waiting loader
     *
     * @return void
     */
    protected function waitLoader()
    {
        $browser = $this;
        $loaderSelector = $this->loaderOld;
        $this->waitUntil(
            function () use ($browser, $loaderSelector) {
                $loader = $browser->find($loaderSelector);
                return $loader->isVisible() == false ? true : null;
            }
        );
    }

    /**
     * Get block by class
     *
     * @param string $class
     * @return mixed
     */
    protected function getClassBlock($class)
    {
        return \Mtf\ObjectManager::getInstance()->create(
            $class,
            ['element' => $this->find($this->selectBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
