<?php
/**
 * Config actions block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Block\System\Config;

use Mtf\Block\Block;

class Actions extends Block
{
    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '#save';

    /**
     * Click "Save" button
     */
    public function clickSave()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }
}
