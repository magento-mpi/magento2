<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Items;

use Mtf\Block\Form;

/**
 * Class ItemForm
 * Gift registry item form on backend
 */
class ItemForm extends Form
{
    /**
     * Fill Gift registry item form
     *
     * @param array $updateOptions
     * @return void
     */
    public function fillForm(array $updateOptions)
    {
        $mapping = $this->dataMapping(array_diff($updateOptions, ['-']));
        $this->_fill($mapping);
    }
}
