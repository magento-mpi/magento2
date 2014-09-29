<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Categories
 * Filling Categories type layout
 */
class Categories extends LayoutForm
{
    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

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
                $this->_rootElement->find($this->chooser)->click();
                $this->getTemplateBlock()->waitLoader();
                $field['value'] = 'Default Category/' . $field['value']['name'];
            } else {
                parent::_fill([$name => $field], $context);
            }
        }
    }

    /**
     * Fixture mapping
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $mapping = parent::dataMapping($fields);
        if (isset($mapping['for'])) {
            $mapping['for']['selector'] = sprintf(
                $mapping['for']['selector'],
                $this->prepareValue($mapping['for']['value'])
            );
            $mapping['for']['value'] = 'Yes';
        }

        return $mapping;
    }

    /**
     * Prepare value for data mapping
     *
     * @param $name
     * @return string
     */
    protected function prepareValue($name)
    {
        $length = strpos(trim($name), ' ');
        if ($length === false) {
            return strtolower($name);
        }
        return strtolower(substr($name, 0, $length));
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
