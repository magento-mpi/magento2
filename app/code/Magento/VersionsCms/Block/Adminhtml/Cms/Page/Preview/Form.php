<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
            [
                'data' => [
                    'id' => 'preview_form',
                    'action' => $this->getUrl('adminhtml/*/drop', ['_current' => true]),
                    'method' => 'post',
                ],
            ]
        );

        if ($data = $this->getFormData()) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $newKey = $key . $subKey;
                        $data[$newKey] = $subValue;
                        $form->addField($newKey, 'hidden', ['name' => $key . "[{$subKey}]"]);
                    }
                    unset($data[$key]);
                } else {
                    $form->addField($key, 'hidden', ['name' => $key]);
                }
            }
            $form->setValues($data);
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
