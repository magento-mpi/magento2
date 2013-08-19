<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search config model
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Model_Config_Source_Search
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
