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
 * Class ChosenOption
 */
class ChosenOption extends Element
{
     /**
     * Select page button selector
     *
     * @var string
     */
    protected $selectButton = '//ancestor::body//div[@class="control"]//button';

    /**
     * Magento varienLoader.js loader
     *
     * @var string
     */
    protected $loaderOld = '//ancestor::body/div[@id="loading-mask"]';

    /**
     * Select page block selector
     *
     * @var string
     */
    protected $pageBlock = "./ancestor::body/div[div/span[contains(text(),'Select Page...')]]";

     /**
     * Select block block selector
     *
     * @var string
     */
    protected $blockSelectBlock = "./ancestor::body/div[div/span[contains(text(),'Select Block...')]]";

    /**
     * Select category block selector
     *
     * @var string
     */
    protected $categorySelectBlock = "./ancestor::body/div[div/span[contains(text(),'Select Category...')]]";

    /**
     * Select category block selector
     *
     * @var string
     */
    protected $productSelectBlock = "./ancestor::body/div[div/span[contains(text(),'Select Product...')]]";

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
            $this->getSelectPageGridBlock()->searchAndOpen(['chooser_identifier' => $value['filter_url_key']]);
        }
        if (isset($value['filter_identifier'])) {
            $this->getSelectBlockGridBlock()->searchAndOpen(['chooser_identifier' => $value['filter_identifier']]);
        }
        if (isset($value['category_path'])) {
            if (isset($value['filter_sku'])) {
                $this->getSelectProductCategoryBlock()->selectCategoryByName($value['category_path']);
                $this->getSelectProductGridBlock()->searchAndOpen(['chooser_sku' => $value['filter_sku']]);
            } else {
                $this->getSelectCategoryBlock()->selectCategoryByName($value['category_path']);
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
        $loaderSelector = '//ancestor::body/div[@id="loading-mask"]';
        $this->waitUntil(
            function () use ($browser, $loaderSelector) {
                $loader = $browser->find($loaderSelector);
                return $loader->isVisible() == false ? true : null;
            }
        );
    }

    /**
     * Get select page grid block
     *
     * @return \Magento\Cms\Test\Block\Adminhtml\Page\Widget\Chooser
     */
    public function getSelectPageGridBlock()
    {
        $block = \Mtf\ObjectManager::getInstance()->create(
            'Magento\Cms\Test\Block\Adminhtml\Page\Widget\Chooser',
            ['element' => $this->find($this->pageBlock, Locator::SELECTOR_XPATH)]
        );

        return $block;
    }

    /**
    * Get select block grid block
    *
    * @return \Magento\Cms\Test\Block\Adminhtml\Page\Widget\Chooser
    */
    public function getSelectBlockGridBlock()
    {
        $block = \Mtf\ObjectManager::getInstance()->create(
            'Magento\Cms\Test\Block\Adminhtml\Page\Widget\Chooser',
            ['element' => $this->find($this->blockSelectBlock, Locator::SELECTOR_XPATH)]
        );

        return $block;
    }

    /**
     * Get select category block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Widget\Chooser
     */
    public function getSelectCategoryBlock()
    {
        $block = \Mtf\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Test\Block\Adminhtml\Category\Widget\Chooser',
            ['element' => $this->find($this->categorySelectBlock, Locator::SELECTOR_XPATH)]
        );

        return $block;
    }

    /**
     * Get select product category block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Widget\Chooser
     */
    public function getSelectProductCategoryBlock()
    {
        $block = \Mtf\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Test\Block\Adminhtml\Category\Widget\Chooser',
            ['element' => $this->find($this->productSelectBlock, Locator::SELECTOR_XPATH)]
        );

        return $block;
    }

    /**
     * Get select product grid block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Widget\Chooser
     */
    public function getSelectProductGridBlock()
    {
        $block = \Mtf\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Test\Block\Adminhtml\Product\Widget\Chooser',
            ['element' => $this->find($this->productSelectBlock, Locator::SELECTOR_XPATH)]
        );

        return $block;
    }
}
