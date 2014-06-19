<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Attributes;

use Mtf\Client\Element;
use Mtf\Client\Driver\Selenium\Element\SuggestElement;

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

//    /**
//     * Fill attribute on the search field
//     *
//     * @param $data
//     * @return void
//     */
//    public function fillSearch($data)
//    {
//        $mapping = $this->dataMapping(['frontend_label' => $data]);
//        $this->_fill($mapping);
//    }
//
//    /**
//     * Get product attribute from attribute search block
//     *
//     * @param Element $element
//     * @return string
//     */
//    public function getSearchAttribute(Element $element = null)
//    {
//        $context = ($element === null) ? $this->_rootElement : $element;
//        $attributeName = $context->find('.mage-suggest-dropdown .ui-corner-all')->getText();
//
//        return $attributeName;
//    }
}
