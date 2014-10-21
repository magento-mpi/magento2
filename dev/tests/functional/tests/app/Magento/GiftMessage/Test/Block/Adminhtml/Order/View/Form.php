<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order\View;

use Mtf\Block\Form as ParentForm;
use Mtf\Client\Element;

/**
 * Class Form
 * Backend item form for gift message.
 */
class Form extends ParentForm
{
    /**
     * Selector for 'Close' button.
     *
     * @var string
     */
    protected $closeButton = '.ui-dialog-titlebar-close';

    /**
     * Close form dialog.
     *
     * @return void
     */
    public function closeDialog()
    {
        $this->_rootElement->find($this->closeButton)->click();
    }
}
