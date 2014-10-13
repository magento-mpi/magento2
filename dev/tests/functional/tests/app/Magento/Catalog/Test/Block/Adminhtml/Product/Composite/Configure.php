<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Composite;

use Magento\Catalog\Test\Block\AbstractConfigureBlock;

/**
 * Class Configure
 * Adminhtml catalog product composite configure block
 */
class Configure extends AbstractConfigureBlock
{
    /**
     * Custom options CSS selector
     *
     * @var string
     */
    protected $customOptionsSelector = '#product_composite_configure_fields_options';

    /**
     * Selector for "Ok" button
     *
     * @var string
     */
    protected $okButton = '.ui-dialog-buttonset button:nth-of-type(2)';

    /**
     * Set quantity
     *
     * @param int $qty
     * @return void
     */
    public function setQty($qty)
    {
        $this->_fill($this->dataMapping(['qty' => $qty]));
    }

    /**
     * Click "Ok" button
     *
     * @return void
     */
    public function clickOk()
    {
        $this->_rootElement->find($this->okButton)->click();
    }
}
