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
 * Theme collection
 */
class Mage_Core_Model_Resource_Theme_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Theme', 'Mage_Core_Model_Resource_Theme');
    }

    /**
     * Add title for parent themes
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function addParentTitle()
    {
        $this->getSelect()->joinLeft(
            array('parent' => $this->getMainTable()),
            'main_table.parent_id = parent.theme_id',
            array('parent_theme_title' => 'parent.theme_title')
        );
        return $this;
    }

    /**
     * Return array for select field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return$this->_toOptionArray('theme_id', 'theme_title');
    }

    /**
     * Return array for grid column
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('theme_id', 'theme_title');
    }

    /**
     * Check whether all themes have non virtual parent theme
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function checkParentInThemes()
    {
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($this as $theme) {
            if ($theme->getParentId()) {
                $theme->setParentId($this->_getParentThemeRecursively($theme->getParentId()));
                $theme->save();
            }
        }
        return $this;
    }

    /**
     * Get parent non virtual theme recursively
     *
     * @param int $parentId
     * @return int|null
     */
    protected function _getParentThemeRecursively($parentId)
    {
        /** @var $parentTheme Mage_Core_Model_Theme */
        $parentTheme = $this->getItemById($parentId);
        if (!$parentTheme->getId() || ($parentTheme->isVirtual() && !$parentTheme->getParentId())) {
            $parentId = null;
        } else if ($parentTheme->isVirtual()) {
            $parentId = $this->_getParentThemeRecursively($parentTheme->getParentId());
        }
        return $parentId;
    }

    /**
     * Get theme from DB by area and theme_path
     *
     * @param string $fullPath
     * @return Mage_Core_Model_Theme
     */
    public function getThemeByFullPath($fullPath)
    {
        list($area, $themePath) = explode('/', $fullPath, 2);
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = Mage::getModel('Mage_Core_Model_Resource_Theme_Collection');
        $collection->addFieldToFilter('area', $area);
        $collection->addFieldToFilter('theme_path', $themePath);

        return $collection->getFirstItem();
    }
}
