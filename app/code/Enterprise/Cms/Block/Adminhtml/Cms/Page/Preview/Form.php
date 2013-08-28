<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Preview Form for revisions
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Preview_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Preparing from for revision page
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Preview_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
                'id' => 'preview_form',
                'action' => $this->getUrl('*/*/drop', array('_current' => true)),
                'method' => 'post'
            ));

        if ($data = $this->getFormData()) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $newKey = $key.$subKey;
                        $data[$newKey] = $subValue;
                        $form->addField($newKey, 'hidden', array('name' => $key."[$subKey]"));
                    }
                    unset($data[$key]);
                } else {
                    $form->addField($key, 'hidden', array('name' => $key));
                }
            }
            $form->setValues($data);
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
