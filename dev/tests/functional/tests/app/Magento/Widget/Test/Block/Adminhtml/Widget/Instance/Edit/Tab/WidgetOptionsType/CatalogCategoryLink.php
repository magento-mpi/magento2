<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogCategoryLink\Form;

/**
 * Class CatalogCategoryLink
 * Filling Widget Options that have catalog category link type
 */
class CatalogCategoryLink extends WidgetOptionsForm
{
    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Cms Page Link grid block
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $cmsPageLinkGrid = '//*[@class="page-wrapper"]/ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';
    // @codingStandardsIgnoreEnd

    /**
     * Fill specified form data
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $name => $field) {
            if ($name == 'entities') {
                $this->_rootElement->find($this->selectPage)->click();

                /** @var Form $catalogCategoryLinkForm  */
                $catalogCategoryLinkForm = $this->blockFactory->create(
                    __NAMESPACE__ . '\CatalogCategoryLink\Form',
                    [
                        'element' => $this->_rootElement
                            ->find($this->cmsPageLinkGrid, Locator::SELECTOR_XPATH)
                    ]
                );
                $elementNew = $this->_rootElement->find($this->cmsPageLinkGrid, Locator::SELECTOR_XPATH);
                $field['value'] = 'Default Category/' . $field['value'];
                $categoryFields['entities'] = $field;
                $catalogCategoryLinkForm->_fill($categoryFields, $elementNew);
                $this->getTemplateBlock()->waitLoader();

            } elseif (!isset($field['value'])) {
                $this->_fill($field, $context);
            } else {
                $element = $this->getElement($context, $field);
                if ($this->mappingMode || ($element->isVisible() && !$element->isDisabled())) {
                    $element->setValue($field['value']);
                    $this->setFields[$name] = $field['value'];
                }
            }
        }
    }

    /**
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Magento\Backend\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
