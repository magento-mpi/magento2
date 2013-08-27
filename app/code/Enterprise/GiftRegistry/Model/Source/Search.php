<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search source model
 */
class Enterprise_GiftRegistry_Model_Source_Search
{
    /**
     * Quick search form types
     */
    const SEARCH_ALL_FORM   = 'all';
    const SEARCH_NAME_FORM  = 'name';
    const SEARCH_EMAIL_FORM = 'email';
    const SEARCH_ID_FORM    = 'id';

    /**
     * Return search form types as option array
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
     * Return array of search form types
     *
     * @return array
     */
    public function getTypes()
    {
        return array(
            self::SEARCH_ALL_FORM => __('All Forms'),
            self::SEARCH_NAME_FORM => __('Recipient Name Search'),
            self::SEARCH_EMAIL_FORM => __('Recipient Email Search'),
            self::SEARCH_ID_FORM => __('Gift Registry ID Search')
        );
    }
}
