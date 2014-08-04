<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Store\Delete;

use Mtf\Client\Element;
use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class StoreForm
 * Form for Store View deletion
 */
class StoreForm extends Form
{
    /**
     * Fill Backup Option in Delete Store View
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
