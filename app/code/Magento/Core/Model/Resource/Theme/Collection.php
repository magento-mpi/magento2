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
 * Theme collection
 */
class Magento_Core_Model_Resource_Theme_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Default page size
     */
    const DEFAULT_PAGE_SIZE = 6;

    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Theme', 'Magento_Core_Model_Resource_Theme');
    }

    /**
     * Add title for parent themes
     *
     * @return Magento_Core_Model_Resource_Theme_Collection
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
     * Add area filter
     *
     * @param string $area
     * @return Magento_Core_Model_Resource_Theme_Collection
     */
    public function addAreaFilter($area = Magento_Core_Model_App_Area::AREA_FRONTEND)
    {
        $this->getSelect()->where('main_table.area=?', $area);
        return $this;
    }

    /**
     * Add type filter in relations
     *
     * @param int $typeParent
     * @param int $typeChild
     * @return Magento_Core_Model_Resource_Theme_Collection
     */
    public function addTypeRelationFilter($typeParent, $typeChild)
    {
        $this->getSelect()->join(
            array('parent' => $this->getMainTable()),
            'main_table.parent_id = parent.theme_id',
            array('parent_type' => 'parent.type')
        )->where('parent.type = ?', $typeParent)->where('main_table.type = ?', $typeChild);
        return $this;
    }

    /**
     * Add type filter
     *
     * @param string|array $type
     * @return Magento_Core_Model_Resource_Theme_Collection
     */
    public function addTypeFilter($type)
    {
        $this->addFieldToFilter('main_table.type', array('in' => $type));
        return $this;
    }

    /**
     * Filter visible themes in backend (physical and virtual only)
     *
     * @return Magento_Core_Model_Resource_Theme_Collection
     */
    public function filterVisibleThemes()
    {
        $this->addTypeFilter(array(Magento_Core_Model_Theme::TYPE_PHYSICAL, Magento_Core_Model_Theme::TYPE_VIRTUAL));
        return $this;
    }

    /**
     * Return array for select field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('theme_id', 'theme_title');
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
     * Get theme from DB by area and theme_path
     *
     * @param string $fullPath
     * @return Magento_Core_Model_Theme
     */
    public function getThemeByFullPath($fullPath)
    {
        $this->_reset()->clear();
        list($area, $themePath) = explode('/', $fullPath, 2);
        $this->addFieldToFilter('area', $area);
        $this->addFieldToFilter('theme_path', $themePath);

        return $this->getFirstItem();
    }

    /**
     * Set page size
     *
     * @param int $size
     * @return $this
     */
    public function setPageSize($size = self::DEFAULT_PAGE_SIZE)
    {
        return parent::setPageSize($size);
    }

    /**
     * Update all child themes relations
     *
     * @param Magento_Core_Model_Theme $themeModel
     * @return $this
     */
    public function updateChildRelations(Magento_Core_Model_Theme $themeModel)
    {
        $parentThemeId = $themeModel->getParentId();
        $this->addFieldToFilter('parent_id', array('eq' => $themeModel->getId()))->load();

        /** @var $theme Magento_Core_Model_Theme */
        foreach ($this->getItems() as $theme) {
            $theme->setParentId($parentThemeId)->save();
        }
        return $this;
    }

    /**
     * Filter frontend physical theme.
     * All themes or per page if set page and page size (page size is optional)
     *
     * @param int $page
     * @param int $pageSize
     * @return $this
     */
    public function filterPhysicalThemes(
        $page = null,
        $pageSize = Magento_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE
    ) {

        $this->addAreaFilter(Magento_Core_Model_App_Area::AREA_FRONTEND)
            ->addTypeFilter(Magento_Core_Model_Theme::TYPE_PHYSICAL);
        if ($page) {
            $this->setPageSize($pageSize)->setCurPage($page);
        }
        return $this;
    }

    /**
     * Filter theme customization
     *
     * @param string $area
     * @param int $type
     * @return $this
     */
    public function filterThemeCustomizations(
        $area = Magento_Core_Model_App_Area::AREA_FRONTEND,
        $type = Magento_Core_Model_Theme::TYPE_VIRTUAL
    ) {
        $this->addAreaFilter($area)->addTypeFilter($type);
        return $this;
    }
}
