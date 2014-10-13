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
     * Category Link block
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $cmsCategoryLink = '//*[@class="page-wrapper"]/ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';
    // @codingStandardsIgnoreEnd

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
        if (isset($mapping['entities'])) {
            $entities = $mapping['entities'];
            unset($mapping['entities']);
        }
        $this->_fill($mapping, $element);

        if (!empty($entities)) {
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
            $entities['value'] = $entities['value'][0]->getPath() . '/' . $entities['value'][0]->getName();
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
