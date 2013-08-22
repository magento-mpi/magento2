<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme files collection
 */
class Magento_Core_Model_Resource_Theme_File_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Theme_File', 'Magento_Core_Model_Resource_Theme_File');
    }

    /**
     * Add select order
     *
     * $field is properly quoted, lately it was treated field "order" as special SQL word and was not working
     *
     * @param string $field
     * @param string $direction
     * @return Magento_Core_Model_Resource_Theme_File_Collection|Magento_Data_Collection|Magento_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return parent::setOrder($this->getConnection()->quoteIdentifier($field), $direction);
    }

    /**
     * Set default order
     *
     * @param string $direction
     * @return Magento_Core_Model_Resource_Theme_File_Collection
     */
    public function setDefaultOrder($direction = self::SORT_ORDER_ASC)
    {
        return $this->setOrder('sort_order', $direction);
    }

    /**
     * Filter out files that do not belong to a theme
     *
     * @param Magento_Core_Model_Theme $theme
     * @return Magento_Core_Model_Resource_Theme_File_Collection
     */
    public function addThemeFilter(Magento_Core_Model_Theme $theme)
    {
        $this->addFieldToFilter('theme_id', $theme->getId());
        return $this;
    }
}
