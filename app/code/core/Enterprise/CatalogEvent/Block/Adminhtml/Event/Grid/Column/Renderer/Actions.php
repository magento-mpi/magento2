<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Events grid actions renderer
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */

class Enterprise_CatalogEvent_Block_Adminhtml_Event_Grid_Column_Renderer_Actions
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renders Action column
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = array();
        $actions[] = array('url'     => $this->getUrl('*/*/edit') . 'id/$event_id',
                           'caption' => $this->helper('Enterprise_CatalogEvent_Helper_Data')->__('Edit'));
        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
