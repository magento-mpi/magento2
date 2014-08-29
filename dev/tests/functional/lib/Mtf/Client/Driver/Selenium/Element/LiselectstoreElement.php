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
    protected $websiteSelector = '(.//li[a[@data-role="website-id"]])[%d]';

    /**
     * Store selector
     *
     * @var string
     */
    protected $storeSelector = '(.//li[@class = "store-switcher-store disabled"])[%d]';

    /**
     * StoreView selector
     *
     * @var string
     */
    protected $storeViewSelector = './/li[a[@data-role="store-view-id"]]';

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
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria('css selector');
        $criteria->value('li');
        $elements = $this->_getWrappedElement()->elements($criteria);
        $dropdownData = [];
        $data = [];
        foreach ($elements as $element) {
            $class = $element->attribute('class');
            $dropdownData[] = [
                'element' => $element,
                'storeView' => $this->isSubstring($class, "store-switcher-store-view"),
                'store' => $this->isSubstring($class, "store-switcher-store "),
                'website' => $this->isSubstring($class, "store-switcher-website"),
            ];
        }
        foreach ($dropdownData as $key => $dropdownElement) {
            if ($dropdownElement['storeView']) {
                $data[] = $this->findNearestElement('website', $key, $dropdownData) . "/"
                . $this->findNearestElement('store', $key, $dropdownData) . "/"
                . $dropdownElement['element']->text();
            }
        }
        return $data;
    }

    /**
     * Check if string contains substring
     *
     * @param string $haystack
     * @param string $pattern
     * @return bool
     */
    protected function isSubstring($haystack, $pattern)
    {
        return preg_match("/$pattern/", $haystack) != 0 ? true : false;
    }

    /**
     * Return nearest elements name according to criteria
     *
     * @param string $criteria
     * @param string $key
     * @param array $elements
     * @return bool
     */
    protected function findNearestElement($criteria, $key, array $elements)
    {
        $elementText = false;
        while ($elementText == false) {
            $elementText = $elements[$key][$criteria] == true ? $elements[$key]['element']->text() : false;
            $key--;
        }
        return $elementText;
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
