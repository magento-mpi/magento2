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
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;
use Magento\VersionsCms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\HierarchyNodeLinkForm\Form;

/**
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
    protected $hierarchyNodeLinkForm = './ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';

    /**
     * Select node on widget options tab
     *
     * @param array $entities
     * @return void
     */
    protected function selectEntity(array $entities)
    {
        foreach ($entities['value'] as $entity) {
            $this->_rootElement->find($this->selectPage)->click();
            $this->getTemplateBlock()->waitLoader();

            // @codingStandardsIgnoreStart
            /** @var Form $hierarchyNodeLinkForm */
            $hierarchyNodeLinkForm = $this->blockFactory->create(
                'Magento\VersionsCms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\HierarchyNodeLinkForm\Form',
                [
                    'element' => $this->_rootElement
                        ->find($this->hierarchyNodeLinkForm, Locator::SELECTOR_XPATH)
                ]
            );
            // @codingStandardsIgnoreEnd
            $elementNew = $this->_rootElement->find($this->hierarchyNodeLinkForm, Locator::SELECTOR_XPATH);
            $entities['value'] = $entity->getIdentifier();
            $hierarchyFields['entities'] = $entities;
            $hierarchyNodeLinkForm->_fill($hierarchyFields, $elementNew);
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
