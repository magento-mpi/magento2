<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Customer;

use Mtf\Block\Form;

/**
 * Class Sharing
 * Sharing wishlist form
 */
class Sharing extends Form
{
    /**
     * Share Wishlist button selector
     *
     * @var string
     */
    protected $shareWishlist = '[type="submit"]';

    /**
     * Click Share Wishlist
     *
     * @return void
     */
    public function shareWishlist()
    {
        $this->_rootElement->find($this->shareWishlist)->click();
    }

    /**
     * Fill Sharing Information form
     *
     * @param array $sharingInfo
     * @return void
     */
    public function fillForm(array $sharingInfo)
    {
        $mapping = $this->dataMapping($sharingInfo);
        $this->_fill($mapping);
    }
}
