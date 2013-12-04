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
 * Typified element class for JQuery Tree elements
 * TODO: Current implementation must be completely changed to support all required JS tree functionality.
 */
class JquerytreeElement extends Element
{
    /**
     * Css locator of JS tree
     *
     * @var string
     */
    protected $nodeCssClass = 'li[class*=jstree]';

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        /** TODO: Add processing of multiple ACL items */
        $selectedResourcesLocator = 'li[class*="jstree-checked"]>a';
        $aclResourceName = ltrim($this->find($selectedResourcesLocator)->getText());
        return array($aclResourceName);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $values = is_array($value) ? $value : array($value);
        foreach ($values as $resourceName) {
            $this->selectCheckbox($resourceName);
        }
    }

    /**
     * Select checkbox.
     *
     * @param $resourceName
     */
    protected function selectCheckbox($resourceName)
    {
        $checkBoxXpath = '//div[contains(@class, "jstree")]//a[text()="' . $resourceName . '"]';
        $this->find($checkBoxXpath, Locator::SELECTOR_XPATH)->click();
    }
}
