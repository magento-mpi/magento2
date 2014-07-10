<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Variations;

use Mtf\Client\Element;
use Mtf\Client\Driver\Selenium\Element\SuggestElement;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Class FormAttributeSearch
 * Form Attribute Search on Product page
 */
class Search extends SuggestElement
{
    /**
     * Attribute Set locator
     *
     * @var string
     */
    protected $value = '.action-toggle > span';

    /**
     * Attribute Set button
     *
     * @var string
     */
    protected $actionToggle = '.action-toggle';

    /**
     * Search attribute result locator
     *
     * @var string
     */
    protected $searchResult = '.mage-suggest-dropdown .ui-corner-all';

    /**
     * Set value
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->find($this->actionToggle)->click();
        parent::setValue($value);
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->find($this->value)->getText();
    }

    /**
     * Checking not exist configurable attribute in search result
     *
     * @param CatalogProductAttribute $productAttribute
     * @return bool
     */
    public function isExistAttributeInSearchResult(CatalogProductAttribute $productAttribute)
    {
        $attribute = $productAttribute->getFrontendLabel();
        $searchResult = $this->find($this->searchResult);

        $this->find($this->suggest)->setValue($attribute);
        if (!$searchResult->isVisible()) {
            return false;
        }
        if ($searchResult->getText() == $attribute) {
            return true;
        }
        return false;
    }
}
