<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display grid with tax information
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_TaxGrid extends Saas_PrintedTemplate_Block_Widget_AbstractGrid
{
    /**
     * Initializes object
     *
     * @see Magento_Core_Block_Template::_construct()
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::widget/tax_grid.phtml');
        $this->_styleMap['header'] = array(
            'font-family' => array('header_font_family', 'font_family'),
            'font-size'   => array('header_font_size',   'size_pt'),
            'font-style'  => array('header_font_italic', 'font_style'),
            'font-weight' => array('header_font_bold',   'font_weight'),
        );
        $this->_styleMap['item'] = array(
            'font-family' => array('row_font_family', 'font_family'),
            'font-size'   => array('row_font_size',   'size_pt'),
            'font-style'  => array('row_font_italic', 'font_style'),
            'font-weight' => array('row_font_bold',   'font_weight'),
        );
    }

    /**
     * Retruns aggregated info of taxes of entity
     * or empty array if info is not accessible
     *
     * @return Traversable
     */
    public function getAggregatedInfo()
    {
        return ($this->getEntity() instanceof Saas_PrintedTemplate_Model_Variable_Abstract_Entity)
            ? $this->getEntity()->getTaxesGroupedByCompoundId()
            : array();
    }

    /**
     * Check if tax widget allowed for current entity type
     *
     * @return string HTML
     * @see Magento_Core_Block_Template::_toHtml()
     */
    protected function _toHtml()
    {
        $isAllowed = ($this->getEntity() instanceof Saas_PrintedTemplate_Model_Variable_Abstract_Entity)
            && in_array($this->getEntity()->getType(), array('invoice', 'creditmemo'));

        return $isAllowed ? parent::_toHtml() : '';
    }
}
