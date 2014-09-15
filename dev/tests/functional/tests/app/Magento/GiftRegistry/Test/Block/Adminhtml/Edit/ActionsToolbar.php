<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Edit;

use Mtf\Block\Block;

/**
 * Class ActionsToolbar
 * Gift registry backend actions block
 */
class ActionsToolbar extends Block
{
    /**
     * "Delete Registry" button
     *
     * @var string
     */
    protected $deleteRegistry = '.delete';

    /**
     * Click on "Delete Registry" button
     *
     * @return void
     */
    public function delete()
    {
        $this->_rootElement->find($this->deleteRegistry)->click();
        $this->_rootElement->acceptAlert();
    }
}
