<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item selector in wishlist table
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column;

class Selector
    extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    /**
     * Render block
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_wishlistData->isMultipleEnabled() || $this->getIsEnabled();
    }

    /**
     * Retrieve column title
     *
     * @return string
     */
    public function getTitle()
    {
        return '<input type="checkbox" id="select-all" />';
    }

    /**
     * Get block javascript
     *
     * @return string
     */
    public function getJs()
    {
        return parent::getJs() . "
            var selector = $('select-all'),
                checkboxes = $(selector).up('#wishlist-table').select('.select'),
                counter = 0;
            if (!checkboxes.length) {
                selector.hide();
            }
            selector.setCounter = function (newVal) {
                counter = newVal;
                this.checked = (counter >= checkboxes.length);
            }
            selector.onclick = function(){
                checkboxes.each( (function(checkbox) {
                    checkbox.checked = this.checked;
                }).bind(this));
                counter = this.checked ? checkboxes.length : 0
            };
            checkboxes.each( function(checkbox) {
                checkbox.onclick = function() {
                    selector.setCounter(this.checked ? counter + 1: counter -1);
                }
            });
        ";
    }
}
