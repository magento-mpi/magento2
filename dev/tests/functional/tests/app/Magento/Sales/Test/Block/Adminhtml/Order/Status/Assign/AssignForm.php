<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Status\Assign;

use Mtf\Block\Form;

/**
 * Class AssignForm
 * OrderStatus Assign Form
 */
class AssignForm extends Form
{
    /**
     * Fill assign form
     *
     * @param array $fields
     * @return void
     */
    public function fillForm(array $fields)
    {
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping);
    }
}
