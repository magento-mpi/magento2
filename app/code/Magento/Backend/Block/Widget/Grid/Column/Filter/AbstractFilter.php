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
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

class AbstractFilter extends \Magento\Backend\Block\AbstractBlock
    implements \Magento\Backend\Block\Widget\Grid\Column\Filter\FilterInterface
{

    /**
     * Column related to filter
     *
     * @var \Magento\Backend\Block\Widget\Grid\Column
     */
    protected $_column;

    /**
     * Set column related to filter
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return \Magento\Backend\Block\Widget\Grid\Column\Filter\AbstractFilter
     */
    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

    /**
     * Retrieve column related to filter
     *
     * @return \Magento\Backend\Block\Widget\Grid\Column
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
        $helper = \Mage::getResourceHelper('Magento_Core');
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
