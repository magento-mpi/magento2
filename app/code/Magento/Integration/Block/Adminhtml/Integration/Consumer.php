<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration;

/**
 * Main Integration properties edit form
 *
 * @category   Magento
 * @package    Magento_Integration
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Consumer extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Set form id prefix, declare fields for integration consumer modal
     *
     * @return \Magento\Integration\Block\Adminhtml\Integration\Consumer
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'integration_consumer_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = $this->_coreRegistry->registry('current_integration');

        $fieldset = $form->addFieldset('base_fieldset');

        if ($model->getIntegrationId()) {
            $fieldset->addField('integration_id', 'hidden', array(
                'name' => 'integration_id',
            ));
        }

        $fieldset->addField('key', 'text', array(
            'label'     => __('Key'),
            'name'      => 'key',
            'required'  => true,
            'disabled'  => true
        ));

        $fieldset->addField('secret', 'text', array(
            'label'     => __('Secret'),
            'name'      => 'secret',
            'required'  => true,
            'disabled'  => true
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }
}
