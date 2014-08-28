<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class LiselectstoreElement
 * Typified element class for lists selectors
 *
 */
class LiselectstoreElement extends Element
{
    /**
     * Template for each element of option
     *
     * @var string
     */
    protected $optionMaskElement = 'li[*[contains(text(), "%s")]]';

    /**
     * Additional part for each child element of option
     *
     * @var string
     */
    protected $optionMaskFollowing = '/following-sibling::';

    /**
     * Website selector
     *
     * @var string
     */
    protected $websiteSelector = '[data-role="website-id"]';

    /**
     * Store selector
     *
     * @var string
     */
    protected $storeSelector = 'span';

    /**
     * StoreView selector
     *
     * @var string
     */
    protected $storeViewSelector = '[data-role="store-view-id"]';

    /**
     * Toggle element selector
     *
     * @var string
     */
    protected $toggleSelector = '.toggle';

    /**
     * Select value in liselect dropdown
     *
     * @param array $value
     * @throws \Exception
     */
    public function setValue($value)
    {
        $this->_context->find($this->toggleSelector)->click();

        $optionSelector = array();
        foreach ($value as $key => $option) {
            $optionSelector[] = sprintf($this->optionMaskElement, $value[$key]);
        }
        $optionSelector = './/' . implode($this->optionMaskFollowing, $optionSelector) . '/a';

        $option = $this->_context->find($optionSelector, Locator::SELECTOR_XPATH);
        if (!$option->isVisible()) {
            throw new \Exception('[' . implode('/', $value) . '] option is not visible in store switcher.');
        }
        $option->click();
    }

    /**
     * Get all available store views
     *
     * @return array
     */
    public function getValues()
    {
        $this->_context->find($this->toggleSelector)->click();
        $elements = $this->_context->find('li')->getElements();
        $data = [];
        foreach ($elements as $key => $element) {
            /** var Element $element */
            if ($element->find($this->storeViewSelector)->isVisible()) {
                $prefix = $this->getWebsiteName($key, $elements) . "/" . $this->getStoreName($key, $elements) . "/";
                $data[] = $prefix . $element->getText();
            }
        }
        return $data;
    }

    /**
     * Get StoreView's Store name
     *
     * @param string $key
     * @param array $elements
     * @return string|bool
     */
    protected function getStoreName($key, $elements)
    {
        $storeName = false;
        while ($storeName == false) {
            $store = $elements[$key]->find($this->storeSelector);
            $storeName = $store->isVisible() ? $store->getText() : false;
            $key--;
        }
        return $storeName;
    }

    /**
     * Get StoreView's Website name
     *
     * @param string $key
     * @param array $elements
     * @return string|bool
     */
    protected function getWebsiteName($key, $elements)
    {
        $websiteName = false;
        while ($websiteName == false) {
            $website = $elements[$key]->find($this->websiteSelector);
            $websiteName = $website->isVisible() ? $website->getText() : false;
            $key--;
        }
        return $websiteName;
    }

    /**
     * Get selected store value
     *
     * @return string|void
     */
    public function getValue()
    {
        $selectedStoreView = $this->_context->find($this->toggleSelector)->getText();
        if ($selectedStoreView == 'Default Config') {
           return $selectedStoreView;
        } else {
            $storeViews = $this->getValues();
            foreach ($storeViews as $storeView) {
                if (strpos($storeView, $selectedStoreView) != false) {
                    return $storeView;
                }
            }
        }
    }
}
