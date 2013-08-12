<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * General Tab in New RMA form
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_New_Tab_General extends Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General
{
    /**
     * Create form. Fieldset are being added in child blocks
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $this->setForm($form);
        return $this;
    }

}
