<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin form widget
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Newsletter\Template\Preview;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Preparing from for revision page
     *
     * @return \Magento\Adminhtml\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'preview_form',
                'action' => $this->getUrl('*/*/drop', array('_current' => true)),
                'method' => 'post',
            ))
        );

        if ($data = $this->getFormData()) {

            $mapper = array('preview_store_id' => 'store_id');

            foreach ($data as $key => $value) {
                if (array_key_exists($key, $mapper)) {
                    $name = $mapper[$key];
                } else {
                    $name = $key;
                }
                $form->addField($key, 'hidden', array('name' => $name));
            }
            $form->setValues($data);
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
