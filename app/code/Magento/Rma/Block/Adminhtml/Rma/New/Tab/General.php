<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * General Tab in New RMA form
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_New_Tab_General extends Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General
{
    /**
     * Create form. Fieldset are being added in child blocks
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $form = $this->_createForm();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $this->setForm($form);
        return $this;
    }

}
