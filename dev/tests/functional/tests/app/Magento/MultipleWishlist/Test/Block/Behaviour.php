<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Block;

use Mtf\Block\Form;

/**
 * Class Behaviour
 * Create new wish list form
 */
class Behaviour extends Form
{
    /**
     * Save button button css selector
     *
     * @var string
     */
    protected $saveButton = '[type="submit"]';

    /**
     * Save wish list
     *
     * @return void
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }
}
