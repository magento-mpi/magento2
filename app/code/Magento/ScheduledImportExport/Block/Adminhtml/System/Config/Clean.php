<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Clean now import/export file history button renderer
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_ScheduledImportExport_Block_Adminhtml_System_Config_Clean extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Remove scope label
     *
     * @param  \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $url    = $this->getUrl('*/scheduled_operation/logClean', array(
            'section' => $this->getRequest()->getParam('section')
        ));
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'id'        => 'clean_now',
                'label'     => __('Clean Now'),
                'onclick'   => 'setLocation(\'' . $url . '\')'
            ));

        return $button->toHtml();
    }
}
