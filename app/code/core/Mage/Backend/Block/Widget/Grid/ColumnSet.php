<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_ColumnSet extends Mage_Core_Block_Text_List
{
    /**
     * Retrieve the list of columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->getLayout()->getChildBlocks($this->getNameInLayout());
    }

    /**
     * Set sortability flag for columns
     *
     * @param bool $value
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setSortable($value)
    {
        if (!$value) {
            foreach ($this->getColumns() as $column) {
                $column->setSortable(false);
            }
        }
        return $this;
    }

    /**
     * Set custom renderer type for columns
     *
     * @param string $type
     * @param string $className
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setRendererType($type, $className)
    {
        foreach($this->getColumns() as $column) {
            $column->setRendererType($type, $className);
        }
        return $this;
    }

    /**
     * Set custom filter type for columns
     *
     * @param string $type
     * @param string $className
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setFilterType($type, $className)
    {
        foreach($this->getColumns() as $column) {
            $column->setFilterType($type, $className);
        }
        return $this;
    }

    /**
     * Prepare block for rendering
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $columns = $this->getColumns();
        $last = array_pop($columns);
        $last->addHeaderCssClass('last');
    }
}
