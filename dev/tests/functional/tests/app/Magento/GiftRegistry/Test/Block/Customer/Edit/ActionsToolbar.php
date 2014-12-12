<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Block\Customer\Edit;

use Mtf\Block\Block;

/**
 * Class ActionsToolbar
 * Frontend gift registry edit actions toolbar
 */
class ActionsToolbar extends Block
{
    /**
     * Save button selector
     *
     * @var string
     */
    protected $saveButton = '[id="submit.save"]';

    /**
     * Click to save button
     *
     * @return void
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }
}
