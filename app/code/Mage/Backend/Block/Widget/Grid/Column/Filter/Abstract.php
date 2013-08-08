<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column filter block
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Filter_Abstract extends Mage_Backend_Block_Abstract
    implements Mage_Backend_Block_Widget_Grid_Column_Filter_Interface
{

    /**
     * Column related to filter
     *
     * @var Mage_Backend_Block_Widget_Grid_Column
     */
    protected $_column;

    /**
     * Set column related to filter
     *
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return Mage_Backend_Block_Widget_Grid_Column_Filter_Abstract
     */
    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

    /**
     * Retrieve column related to filter
     *
     * @return Mage_Backend_Block_Widget_Grid_Column
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Retrieve html name of filter
     *
     * @return string
     */
    protected function _getHtmlName()
    {
        return $this->getColumn()->getId();
    }

    /**
     * Retrieve html id of filter
     *
     * @return string
     */
    protected function _getHtmlId()
    {
        return $this->getColumn()->getHtmlId();
    }

    /**
     * Retrieve escaped value
     *
     * @param mixed $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        return htmlspecialchars((string)$this->getValue($index));
    }

    /**
     * Retrieve condition
     *
     * @return array
     */
    public function getCondition()
    {
        $helper = Mage::getResourceHelper('Magento_Core');
        $likeExpression = $helper->addLikeEscape($this->getValue(), array('position' => 'any'));
        return array('like' => $likeExpression);
    }

    /**
     * Retrieve filter html
     *
     * @return string
     */
    public function getHtml()
    {
        return '';
    }
}
