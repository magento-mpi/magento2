<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Preview Form for revisions
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Preparing from for revision page
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview\Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'preview_form',
                'action' => $this->getUrl('*/*/drop', array('_current' => true)),
                'method' => 'post',
            ))
        );

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
