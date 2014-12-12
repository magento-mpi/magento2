<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
