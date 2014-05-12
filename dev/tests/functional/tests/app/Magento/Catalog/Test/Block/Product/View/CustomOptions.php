<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\View;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Custom Options
 *
 */
class CustomOptions extends Block
{
    protected $fieldsetSelector = '.fieldset';
    protected $rowSelector = '.field';

    /**
     * Get options
     *
     * @return array
     */
    public function get()
    {
        $optionsFieldset = $this->_rootElement->find($this->fieldsetSelector);
        $fieldsetIndex = 1;
        $options = array();
        //@todo move to separate block
        $field = $optionsFieldset->find($this->rowSelector . ':nth-of-type(' . $fieldsetIndex . ')');
        while ($field->isVisible()) {
            $optionFieldset = [];
            $optionFieldset['title'] = $field->find('.label')->getText();
            $optionFieldset['is_require'] = $field->find('select.required')->isVisible();
            $options[] = & $optionFieldset;
            $optionIndex = 1;
            //@todo move to separate block
            $option = $field->find('select > option:nth-of-type(' . $optionIndex . ')');
            while ($option->isVisible()) {
                if (preg_match('~^(?<title>.+) .?\$(?P<price>\d+\.\d*)$~', $option->getText(), $matches) !== false
                    && !empty($matches['price'])
                ) {
                    $optionFieldset['options'][] = [
                        'title' => $matches['title'],
                        'price' => $matches['price'],
                    ];
                };
                $optionIndex++;
                $option = $field->find('select > option:nth-of-type(' . $optionIndex . ')');
            }
            $fieldsetIndex++;
            $field = $optionsFieldset->find($this->rowSelector . ':nth-of-type(' . $fieldsetIndex . ')');
        }
        return $options;
    }

}
