<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma;

use Mtf\Block\Block;

/**
 * Class Actions
 * Order actions block
 *
 * @package Magento\Rma\Test\Block\Adminhtml\Rma
 */
class Actions extends Block
{
    /**
     * 'Back' button
     *
     * @var string
     */
    protected $back = '#back';

    /**
     * 'Reset' button
     *
     * @var string
     */
    protected $reset = '#reset';

    /**
     * 'Close' button
     *
     * @var string
     */
    protected $close = '#close';

    /**
     * 'Void' button
     *
     * @var string
     */
    protected $save = '#save';

    /**
     * 'Hold' button
     *
     * @var string
     */
    protected $saveAndEdit = '#save_and_edit_button';

    /**
     * 'Invoice' button
     *
     * @var string
     */
    protected $printButton = '#print';

    /**
     * Go back
     */
    public function back()
    {
        $this->_rootElement->find($this->back)->click();
    }

    /**
     * Reset
     */
    public function reset()
    {
        $this->_rootElement->find($this->reset)->click();
    }

    /**
     * Close
     */
    public function close()
    {
        $this->_rootElement->find($this->close)->click();
    }

    /**
     * Save
     */
    public function save()
    {
        $this->_rootElement->find($this->save)->click();
    }

    /**
     * Save and continue edit
     */
    public function saveAndEdit()
    {
        $this->_rootElement->find($this->saveAndEdit)->click();
    }

    /**
     * print
     */
    public function printButton()
    {
        $this->_rootElement->find($this->printButton)->click();
    }
}
