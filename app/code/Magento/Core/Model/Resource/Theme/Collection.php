<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Theme;

/**
 * Theme collection
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection implements
    \Magento\View\Design\Theme\Label\ListInterface,
    \Magento\View\Design\Theme\ListInterface
{
    /**
     * Default page size
     */
    const DEFAULT_PAGE_SIZE = 6;

    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Theme', 'Magento\Core\Model\Resource\Theme');
    }

    /**
     * Add title for parent themes
     *
     * @return $this
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
     * @return $this
     */
    public function addAreaFilter($area = \Magento\Framework\App\Area::AREA_FRONTEND)
    {
        $this->getSelect()->where('main_table.area=?', $area);
        return $this;
    }

    /**
     * Add type filter in relations
     *
     * @param int $typeParent
     * @param int $typeChild
     * @return $this
     */
    public function addTypeRelationFilter($typeParent, $typeChild)
    {
        $this->getSelect()->join(
            array('parent' => $this->getMainTable()),
            'main_table.parent_id = parent.theme_id',
            array('parent_type' => 'parent.type')
        )->where(
            'parent.type = ?',
            $typeParent
        )->where(
            'main_table.type = ?',
            $typeChild
        );
        return $this;
    }

    /**
     * Add type filter
     *
     * @param string|array $type
     * @return $this
     */
    public function addTypeFilter($type)
    {
        $this->addFieldToFilter('main_table.type', array('in' => $type));
        return $this;
    }

    /**
     * Filter visible themes in backend (physical and virtual only)
     *
     * @return $this
     */
    public function filterVisibleThemes()
    {
        $this->addTypeFilter(
            array(
                \Magento\View\Design\ThemeInterface::TYPE_PHYSICAL,
                \Magento\View\Design\ThemeInterface::TYPE_VIRTUAL
            )
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
     * @return \Magento\Core\Model\Theme
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
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @return $this
     */
    public function updateChildRelations(\Magento\View\Design\ThemeInterface $themeModel)
    {
        $parentThemeId = $themeModel->getParentId();
        $this->addFieldToFilter('parent_id', array('eq' => $themeModel->getId()))->load();

        /** @var $theme \Magento\View\Design\ThemeInterface */
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
        $pageSize = \Magento\Core\Model\Resource\Theme\Collection::DEFAULT_PAGE_SIZE
    ) {

        $this->addAreaFilter(
            \Magento\Framework\App\Area::AREA_FRONTEND
        )->addTypeFilter(
            \Magento\View\Design\ThemeInterface::TYPE_PHYSICAL
        );
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
        $area = \Magento\Framework\App\Area::AREA_FRONTEND,
        $type = \Magento\View\Design\ThemeInterface::TYPE_VIRTUAL
    ) {
        $this->addAreaFilter($area)->addTypeFilter($type);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabels()
    {
        $this->_reset()->clear();
        $labels = $this->setOrder(
            'theme_title',
            \Magento\Data\Collection::SORT_ORDER_ASC
        )->filterVisibleThemes()->addAreaFilter(
            \Magento\Framework\App\Area::AREA_FRONTEND
        );
        return $labels->toOptionArray();
    }
}
