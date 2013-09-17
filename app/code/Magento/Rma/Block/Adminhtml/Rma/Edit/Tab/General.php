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
 * General Tab in Edit RMA form
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Create form. Fieldset are being added in child blocks
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = $this->_coreRegistry->registry('current_rma');

        if ($model) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
