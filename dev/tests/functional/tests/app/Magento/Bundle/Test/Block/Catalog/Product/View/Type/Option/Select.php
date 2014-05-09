<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Select
 * Bundle option dropdown type
 *
 */
class Select extends Form
{
    /**
     * Set data in bundle option
     *
     * @param array $data
     */
    public function fillOption(array $data)
    {
        $this->waitForElementVisible($this->mapping['value']['selector']);

        $select = $this->_rootElement->find($this->mapping['value']['selector'], Locator::SELECTOR_CSS, 'select');
        $select->setValue($data['value']);
        $qtyField = $this->_rootElement->find($this->mapping['qty']['selector']);
        if (!$qtyField->isDisabled()) { //TODO should be remove after fix qty field
            $qtyField->setValue($data['qty']);
        }
    }
}
