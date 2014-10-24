<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\AbstractWidgetOptionsForm;
use Magento\VersionsCms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\HierarchyNodeLinkForm\Form;

/**
 * Class HierarchyNodeLink
 * Filling Widget Options that have hierarchy node link type
 */
class HierarchyNodeLink extends AbstractWidgetOptionsForm
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
            $this->selectNode($mapping['entities']);
        }
    }

    /**
     * Select node on widget options tab
     *
     * @param array $entities
     * @return void
     */
    protected function selectNode(array $entities)
    {
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
        $entities['value'] = $entities['value'][0]->getIdentifier();
        $hierarchyFields['entities'] = $entities;
        $hierarchyNodeLinkForm->_fill($hierarchyFields, $elementNew);
        $this->getTemplateBlock()->waitLoader();
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
