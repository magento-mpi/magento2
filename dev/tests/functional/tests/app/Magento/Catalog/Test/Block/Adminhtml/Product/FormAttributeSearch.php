<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Mtf\Client\Element;
use Mtf\Block\Form as AbstractForm;

/**
 * Class FormAttributeSearch
 * Form Attribute Search on Product page
 */
class FormAttributeSearch extends AbstractForm
{
    /**
     * Fill attribute on the search field
     *
     * @param $data
     * @return void
     */
    public function fillSearch($data)
    {
        $mapping = $this->dataMapping(['frontend_label' => $data]);
        $this->_fill($mapping);
    }

    /**
     * Get product attribute from attribute search block
     *
     * @param Element $element
     * @return string
     */
    public function getSearchAttribute(Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        $attributeName = $context->find('.mage-suggest-dropdown .ui-corner-all')->getText();

        return $attributeName;
    }
}
