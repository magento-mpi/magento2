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
namespace Magento\Core\Model\Resource\Theme\File;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Theme\File', 'Magento\Core\Model\Resource\Theme\File');
    }

    /**
     * Add select order
     *
     * $field is properly quoted, lately it was treated field "order" as special SQL word and was not working
     *
     * @param string $field
     * @param string $direction
     * @return \Magento\Core\Model\Resource\Theme\File\Collection|\Magento\Data\Collection|\Magento\Data\Collection\Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return parent::setOrder($this->getConnection()->quoteIdentifier($field), $direction);
    }

    /**
     * Set default order
     *
     * @param string $direction
     * @return \Magento\Core\Model\Resource\Theme\File\Collection
     */
    public function setDefaultOrder($direction = self::SORT_ORDER_ASC)
    {
        return $this->setOrder('sort_order', $direction);
    }

    /**
     * Filter out files that do not belong to a theme
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return \Magento\Core\Model\Resource\Theme\File\Collection
     */
    public function addThemeFilter(\Magento\Core\Model\Theme $theme)
    {
        $this->addFieldToFilter('theme_id', $theme->getId());
        return $this;
    }
}
