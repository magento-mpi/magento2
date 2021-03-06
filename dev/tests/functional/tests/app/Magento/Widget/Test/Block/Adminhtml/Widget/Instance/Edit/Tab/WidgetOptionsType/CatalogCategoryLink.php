<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogCategoryLink\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Filling Widget Options that have catalog category link type
 */
class CatalogCategoryLink extends WidgetOptionsForm
{
    /**
     * Category Link block
     *
     * @var string
     */
    protected $cmsCategoryLink = './ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';

    /**
     * Select category on widget options tab
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
}
