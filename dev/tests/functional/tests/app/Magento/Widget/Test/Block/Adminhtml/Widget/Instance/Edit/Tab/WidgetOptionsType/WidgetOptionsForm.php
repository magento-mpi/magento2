<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Magento\Backend\Test\Block\Template;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;

/**
 * Responds for filling widget options form
 */
class WidgetOptionsForm extends Form
{
    /**
     * Select page button
     *
     * @var string
     */
    protected $selectPage = '.scalable.btn-chooser';

    /**
     * Select block
     *
     * @var string
     */
    protected $selectBlock = '';

    /**
     * Grid block locator
     *
     * @var string
     */
    protected $gridBlock = '';

    /**
     * Path to grid
     *
     * @var string
     */
    protected $pathToGrid = '';

    /**
     * Selector for template block.
     *
     * @var string
     */
    protected $template = './ancestor::body';

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
            $this->selectEntity($mapping['entities']);
        }
    }

    /**
     * Getting options data form on the widget options form
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        return $this->_getData($mapping, $element);
    }

    /**
     * Select entity on widget options tab
     *
     * @param array $entities
     * @return void
     */
    protected function selectEntity(array $entities)
    {
        foreach ($entities['value'] as $entity) {
            $this->_rootElement->find($this->selectBlock)->click();
            $this->getTemplateBlock()->waitLoader();
            $grid = $this->blockFactory->create(
                $this->pathToGrid,
                [
                    'element' => $this->_rootElement->find($this->gridBlock, Locator::SELECTOR_XPATH)
                ]
            );
            $grid->searchAndSelect($this->prepareFilter($entity));
        }
    }

    /**
     * Prepare filter for grid
     *
     * @param InjectableFixture $entity
     * @return array
     */
    protected function prepareFilter(InjectableFixture $entity)
    {
        return ['title' => $entity->getTitle()];
    }

    /**
     * Get template block.
     *
     * @return Template
     */
    public function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Magento\Backend\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->template, Locator::SELECTOR_XPATH)]
        );
    }
}
