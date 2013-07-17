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
 * Theme files collection
 */
class Mage_Core_Model_Resource_Theme_File_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Theme_File', 'Mage_Core_Model_Resource_Theme_File');
    }

    /**
     * Add select order
     *
     * $field is properly quoted, lately it was treated field "order" as special SQL word and was not working
     *
     * @param string $field
     * @param string $direction
     * @return Mage_Core_Model_Resource_Theme_File_Collection|Varien_Data_Collection|Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return parent::setOrder($this->getConnection()->quoteIdentifier($field), $direction);
    }

    /**
     * Set default order
     *
     * @param string $direction
     * @return Mage_Core_Model_Resource_Theme_File_Collection
     */
    public function setDefaultOrder($direction = self::SORT_ORDER_ASC)
    {
        return $this->setOrder('sort_order', $direction);
    }

    /**
     * Filter out files that do not belong to a theme
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Resource_Theme_File_Collection
     */
    public function addThemeFilter(Mage_Core_Model_Theme $theme)
    {
        $this->addFieldToFilter('theme_id', $theme->getId());
        return $this;
    }
}
