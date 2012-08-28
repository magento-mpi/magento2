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
 * Theme resource model
 *
 * @TODO Will changed(package load and save) after package model refactoring.
 */
class Mage_Core_Model_Resource_Theme extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Package code field
     */
    const FIELD_PACKAGE_CODE = 'package_code';

    /**
     * Package title field
     */
    const FIELD_PACKAGE_TITLE = 'package_title';

    /**
     * Package id field
     */
    const FIELD_PACKAGE_ID = 'package_id';

    /**
     * Package table
     *
     * @var null|string
     */
    protected $_packageTable;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('core_theme', 'theme_id');
    }

    /**
     * Get package table
     *
     * @return string
     */
    protected function _getPackageTable()
    {
        if (null === $this->_packageTable) {
            $this->_packageTable = $this->getTable('core_package');
        }

        return $this->_packageTable;
    }

    /**
     * Before theme save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $package = $this->_loadPackageByAttribute(self::FIELD_PACKAGE_CODE, $object->getPackageCode());

        if (!$package) {
            $this->_addPackage($object);
            $packageId = $this->_getReadAdapter()->lastInsertId($this->_getPackageTable());
        } else {
            $packageId = $package[self::FIELD_PACKAGE_ID];
        }

        $object->setPackageId($packageId);

        return parent::_beforeSave($object);
    }

    /**
     * After theme model load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $this->loadPackageData($object);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Load package data
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Theme
     */
    public function loadPackageData(Mage_Core_Model_Abstract $object)
    {
        $package = $this->_loadPackageByAttribute(self::FIELD_PACKAGE_ID, $object->getPackageId());
        $object->addData($package);
        return $this;
    }

    /**
     * Add theme package
     *
     * @param Mage_Core_Model_Theme $object
     * @return bool
     */
    protected function _addPackage($object)
    {
        $writeAdapter = $this->_getWriteAdapter();
        return $writeAdapter->insert($this->_getPackageTable(), array(
            self::FIELD_PACKAGE_CODE  => $object->getPackageCode(),
            self::FIELD_PACKAGE_TITLE => $object->getPackageTitle()
        ));
    }

    /**
     * Load package by attribute
     *
     * @param string $attribute
     * @param string $value
     * @return array
     */
    protected function _loadPackageByAttribute($attribute, $value)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->_getPackageTable())
            ->where("{$attribute} = ?", $value);
        return $readAdapter->fetchRow($select);
    }
}
