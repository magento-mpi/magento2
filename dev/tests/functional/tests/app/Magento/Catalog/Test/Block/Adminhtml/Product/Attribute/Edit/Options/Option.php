<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Options;

use Mtf\Block\Form;

/**
 * Class Option
 * Form "Option" on tab "Manage Options"
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
