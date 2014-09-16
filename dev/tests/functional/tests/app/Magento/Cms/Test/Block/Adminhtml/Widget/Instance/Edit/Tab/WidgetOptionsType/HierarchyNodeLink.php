<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;
use Magento\Cms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\HierarchyNodeLinkForm\Form;

/**
 * Class HierarchyNodeLink
 * Filling Widget Options that have hierarchy node link type
 */
class HierarchyNodeLink extends WidgetOptionsForm
{
    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Hierarchy Node Link block
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $hierarchyNodeLinkForm = '//*[@class="page-wrapper"]/ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';
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
                $this->getTemplateBlock()->waitLoader();

                /** @var Form $hierarchyNodeLinkForm  */
                $hierarchyNodeLinkForm = $this->blockFactory->create(
                    __NAMESPACE__ . '\HierarchyNodeLinkForm\Form',
                    [
                        'element' => $this->_rootElement
                            ->find($this->hierarchyNodeLinkForm, Locator::SELECTOR_XPATH)
                    ]
                );
                $elementNew = $this->_rootElement->find($this->hierarchyNodeLinkForm, Locator::SELECTOR_XPATH);
                $field['value'] = $field['value']['identifier'];
                $hierarchyFields['entities'] = $field;
                $hierarchyNodeLinkForm->_fill($hierarchyFields, $elementNew);
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
