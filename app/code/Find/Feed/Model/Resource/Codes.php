<?php
/**
 * {license_notice}
 *
 * @category    Find
 * @package     Find_Feed
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Thefind feed codes (attribute map) model
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Resource_Codes extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Class local constructor
     */
    protected function _construct()
    {
        return $this->_init('find_feed_import_codes', 'code_id');
    }
}
