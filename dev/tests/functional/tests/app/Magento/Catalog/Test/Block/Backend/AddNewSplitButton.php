<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Backend;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

class AddNewSplitButton extends Block
{
    /**
     * Product type list
     *
     * @var string
     */
    protected $typeList = '[data-ui-id=products-list-add-new-button-dropdown-menu]';

    /**
     * Product toggle button
     *
     * @var string
     */
    protected $toggleButton = '[data-ui-id=products-list-add-new-button-dropdown]';

    public function getTypeList()
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        return $this->_rootElement->find(
            $this->typeList,
            Locator::SELECTOR_CSS
        )->getText();
    }
}
