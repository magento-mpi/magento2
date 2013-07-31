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
 * Adminhtml system templates page content block
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template extends Mage_Backend_Block_Template
{
    /**
     * Set printed templates grid template
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::list.phtml');
    }

    /**
     * Create add button and grid blocks
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => $this->__('Add New Template'),
                    'onclick'   => "window.location='" . $this->getCreateUrl() . "'",
                    'class'     => 'add'
        )));
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid',
                'printed.template.grid'
            )
        );
        return parent::_prepareLayout();
    }

    /**
     * Get URL for create new printed template
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * Get printed templates page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Printed Templates');
    }

}
