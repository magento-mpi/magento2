<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource setup model with methods needed for migration process between Magento versions
 */
class Mage_Core_Model_Resource_Setup_Migration extends Mage_Core_Model_Resource_Setup
{
    const FIELD_CONTENT_TYPE_PLAIN = 'plain';
    const FIELD_CONTENT_TYPE_XML   = 'xml';
    const FIELD_CONTENT_TYPE_WIKI  = 'wiki';

    protected $_rowsPerPage = 100;

    public function updateClassAliases($tableName, $fieldName, $entityType,
        $fieldContentType = self::FIELD_CONTENT_TYPE_PLAIN
    ) {
        $pagesCount = ceil($this->_getRowsCount() / $this->_rowsPerPage);

        for ($page = 1; $page <= $pagesCount; $page++) {
            $this->_updateClassAliasesForPage($tableName, $fieldName, $entityType, $fieldContentType, $page);
        }
    }

    protected function _getRowsCount()
    {

    }

    protected function _updateClassAliasesForPage($tableName, $fieldName, $entityType,
        $fieldContentType = self::FIELD_CONTENT_TYPE_PLAIN, $currPage
    ) {
        $rowsData = $this->_getRowsData($tableName, $fieldName, $currPage);

        foreach ($rowsData as $rowData) {
            $this->_replaceAlias($rowData, $tableName, $fieldName, $entityType, $fieldContentType);
        }
    }

    protected function _getRowsData($tableName, $fieldName, $currPage)
    {
        return array();
    }

    protected function _replaceAlias($data, $tableName, $fieldName, $entityType, $fieldContentType)
    {

    }
}
