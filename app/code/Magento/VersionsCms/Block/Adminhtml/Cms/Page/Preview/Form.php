<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

/**
 * Preview Form for revisions
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Preparing from for revision page
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            array(
                'data' => array(
                    'id' => 'preview_form',
                    'action' => $this->getUrl('adminhtml/*/drop', array('_current' => true)),
                    'method' => 'post'
                )
            )
        );

        if ($data = $this->getFormData()) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $newKey = $key . $subKey;
                        $data[$newKey] = $subValue;
                        $form->addField($newKey, 'hidden', array('name' => $key . "[{$subKey}]"));
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
