<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Options;

use Mtf\Client\Element;
use Mtf\Block\Form;

/**
 * Class OptionDropDown
 * Form "Option dropdown" on tab product "Custom options"
 */
class Option extends Form
{
    /**
     * Fill the form
     *
     * @param array $fields
     * @return void
     */
    public function fillOptions(array $fields)
    {
        $data = $this->dataMapping($fields);
        $this->_fill($data);
    }
}
