<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget;

use Mtf\Block\Block;

/**
 * Class PageAction
 * Page action block on widget edit page
 */
class PageAction extends Block
{
    /**
     * Button "Delete" selector
     *
     * @var string
     */
    protected $deleteButton = '#delete';

    /**
     * Delete wish list
     *
     * @return void
     */
    public function deleteWishlist()
    {
        $this->_rootElement->find($this->deleteButton)->click();
        $this->_rootElement->acceptAlert();
    }
}
