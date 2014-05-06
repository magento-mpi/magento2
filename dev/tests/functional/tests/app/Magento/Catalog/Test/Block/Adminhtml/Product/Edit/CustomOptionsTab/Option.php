<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Block\Block;
use Mtf\Factory\Factory;

/**
 * Select Type
 */
class Option extends Block
{
    /**
     * Create block of special type
     *
     * @param string $type
     * @param Element $element
     * @throws \InvalidArgumentException
     * @return Block|\Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab\TypeSelect
     */
    protected function factory($type, Element $element)
    {
        switch ($type) {
            case 'Drop-down':
                return Factory::getBlockFactory()
                    ->getMagentoCatalogAdminhtmlProductEditCustomOptionsTabTypeSelect($element);
                break;
            default:
                throw new \InvalidArgumentException('Option type is not set');
        }
    }

    /**
     * Fill
     *
     * @param array $data
     */
    public function fill($data)
    {
        $this->_rootElement->find('.fieldset-alt [name$="[title]"]')
            ->setValue($data['title']);
        $this->_rootElement->find('.fieldset-alt [name$="[type]"]', Locator::SELECTOR_CSS, 'select')
            ->setValue($data['type']);

        $addButton = $this->_rootElement->find('.add-select-row');
        $table = $this->_rootElement->find('.data-table');
        foreach ($data['options'] as $index => $value) {
            $addButton->click();
            $subRow = $table->find('tbody tr:nth-child(' . ($index + 1) . ')');
            $this->factory($data['type'], $subRow)->fill($value);
        }
    }
}
