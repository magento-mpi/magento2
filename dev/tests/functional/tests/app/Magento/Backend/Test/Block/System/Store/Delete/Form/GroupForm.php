<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Store\Delete\Form;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Class GroupForm
 * Form for Store Group deleting
 */
class GroupForm extends Form
{
    /**
     * Fill Backup Option in Delete Store Group
     *
     * @param array $data
     * @param Element $element
     * @return void
     */
    public function fillForm(array $data, Element $element = null)
    {
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping, $element);
    }
}
