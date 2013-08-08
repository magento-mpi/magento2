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
 * Adminhtml newsletter templates grid block action item renderer
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_Grid_Renderer_Action extends
    Mage_Backend_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renders available actions
     *
     * @param  Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = array();

        $actions[] = array(
            'url'     =>  $this->getUrl('*/*/previewHtml', array('id' => $row->getId())),
            'popup'   =>  true,
            'caption' => __('Preview HTML')
        );

        $actions[] = array(
            'url'     =>  $this->getUrl('*/*/previewPdf', array('id' => $row->getId())),
            'popup'   =>  true,
            'caption' => __('Preview PDF')
        );

        return $this->_actionsToHtml($actions, $row);
    }

    /**
     * Creates html for actions
     * Every actions is a link
     *
     * @param array $actions
     * @param Varien_Object $row
     * @return  string
     */
    protected function _actionsToHtml(array $actions, Varien_Object $row)
    {
        $links = array();
        foreach ($actions as $action) {
            if ( is_array($action) ) {
                $links[] = $this->_toLinkHtml($action, $row);
            }
        }

        return implode('<br />', $links);
    }
}
