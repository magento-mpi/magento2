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
 * Search config model
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model\Config\Source;

class Search
{
    /**
     * Quick search form types
     */
    const WISHLIST_SEARCH_DISPLAY_ALL_FORMS = 'all';
    const WISHLIST_SEARCH_DISPLAY_NAME_FORM = 'name';
    const WISHLIST_SEARCH_DISPLAY_EMAIL_FORM = 'email';

    /**
     * Retrieve search form types as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        foreach ($this->getTypes() as $key => $label) {
            $result[] = array('value' => $key, 'label' => $label);
        }
        return $result;
    }

    /**
     * Retrieve array of search form types
     *
     * @return array
     */
    public function getTypes()
    {
        return array(
            self::WISHLIST_SEARCH_DISPLAY_ALL_FORMS => __('All Forms'),
            self::WISHLIST_SEARCH_DISPLAY_NAME_FORM => __('Wish List Owner Name Search'),
            self::WISHLIST_SEARCH_DISPLAY_EMAIL_FORM => __('Wish List Owner Email Search')
        );
    }
}
