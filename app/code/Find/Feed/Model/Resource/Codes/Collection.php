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
 * TheFind feed codes (attribute map) collection
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Resource_Codes_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Local constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Find_Feed_Model_Codes', 'Find_Feed_Model_Resource_Codes');
    }

    /**
     * Fetch attributes to import
     *
     * @return array
     */
    public function getImportAttributes()
    {
        $this->addFieldToFilter('is_imported', array('eq' => '1'));
        return $this->_toOptionHash('import_code', 'eav_code');
    }

}
