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
     * Category Link block
     *
     * @var string
     */
    protected $cmsCategoryLink = './ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';

    /**
     * Filling widget options form
     *
     * @param array $widgetOptionsFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $widgetOptionsFields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($widgetOptionsFields);
        $this->_fill(array_diff_key($mapping, ['entities' => '']), $element);
        if (isset($mapping['entities'])) {
            $this->selectCategory($mapping['entities']);
        }
    }

    /**
     * Select category on widget options tab
     *
     * @param array $entities
     * @return void
     */
    protected function selectCategory(array $entities)
    {
        foreach ($entities['value'] as $entity) {
            $this->_rootElement->find($this->selectPage)->click();
            $this->getTemplateBlock()->waitLoader();
            // @codingStandardsIgnoreStart
            /** @var Form $catalogCategoryLinkForm */
            $catalogCategoryLinkForm = $this->blockFactory->create(
                'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogCategoryLink\Form',
                [
                    'element' => $this->_rootElement
                        ->find($this->cmsCategoryLink, Locator::SELECTOR_XPATH)
                ]
            );
            // @codingStandardsIgnoreEnd
            $elementNew = $this->_rootElement->find($this->cmsCategoryLink, Locator::SELECTOR_XPATH);
            $entities['value'] = $entity->getPath() . '/' . $entity->getName();
            $categoryFields['entities'] = $entities;
            $catalogCategoryLinkForm->_fill($categoryFields, $elementNew);
            $this->getTemplateBlock()->waitLoader();
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
