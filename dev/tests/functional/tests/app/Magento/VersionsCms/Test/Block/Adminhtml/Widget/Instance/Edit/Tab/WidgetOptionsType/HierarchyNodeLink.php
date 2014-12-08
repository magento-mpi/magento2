<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Magento\VersionsCms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\HierarchyNodeLinkForm\Form;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Filling Widget Options that have hierarchy node link type
 */
class HierarchyNodeLink extends WidgetOptionsForm
{
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
}
