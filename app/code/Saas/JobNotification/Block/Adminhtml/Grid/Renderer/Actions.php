<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_JobNotification_Block_Adminhtml_Grid_Renderer_Actions
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * @var Saas_JobNotification_Block_Adminhtml_Grid_Renderer_Actions_Filter
     */
    protected $_filter;

    /**
     * @param Mage_Backend_Block_Context $context
     * @param Saas_JobNotification_Block_Adminhtml_Grid_Renderer_Actions_Filter $filter
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Context $context,
        Saas_JobNotification_Block_Adminhtml_Grid_Renderer_Actions_Filter $filter,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_filter = $filter;
    }

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $output = array();

        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        foreach ($actions as $actionConfig) {
            if (is_array($actionConfig) && $this->_filter->isAllowed($actionConfig, $row)) {
                $output[] = $this->_toLinkHtml($actionConfig, $row);
            }
        }
        return implode(' | ', $output);
    }
}
