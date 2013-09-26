<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column filter block
 */
class Magento_Backend_Block_Widget_Grid_Column_Filter_Abstract extends Magento_Backend_Block_Abstract
    implements Magento_Backend_Block_Widget_Grid_Column_Filter_Interface
{
    /**
     * Column related to filter
     *
     * @var Magento_Backend_Block_Widget_Grid_Column
     */
    protected $_column;

    /**
     * @var Magento_Core_Model_Resource_Helper_Mysql4
     */
    protected $_resourceHelper;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper,
        array $data = array()
    ) {
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($context, $data);
    }

    /**
     * Set column related to filter
     *
     * @param Magento_Backend_Block_Widget_Grid_Column $column
     * @return Magento_Backend_Block_Widget_Grid_Column_Filter_Abstract
     */
    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

    /**
     * Retrieve column related to filter
     *
     * @return Magento_Backend_Block_Widget_Grid_Column
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
        $likeExpression = $this->_resourceHelper->addLikeEscape($this->getValue(), array('position' => 'any'));
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
