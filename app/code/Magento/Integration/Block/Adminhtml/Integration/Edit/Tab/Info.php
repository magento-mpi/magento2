<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration\Edit\Tab;

use \Magento\Integration\Controller\Adminhtml\Integration;

/**
 * Main Integration properties edit form
 *
 * @category   Magento
 * @package    Magento_Integration
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Info extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Set form id prefix, declare fields for integration properties
     *
     * @return \Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'integration_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $integrationData = $this->_coreRegistry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION);
        $fieldset = $form->addFieldset('base_fieldset');
        if ($integrationData[Integration::DATA_INTEGRATION_ID]) {
            $fieldset->addField(
                Integration::DATA_INTEGRATION_ID,
                'hidden',
                array(
                    'name' => 'id',
                )
            );
        }
        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => __('Integration Name'),
                'name' => 'name',
                'required' => true,
                'disabled' => false
            )
        );
        $fieldset->addField(
            'email',
            'text',
            array(
                'label' => __('Email'),
                'name' => 'email',
                'required' => true,
                'disabled' => false,
                'class' => 'validate-email',
            )
        );
        $fieldset->addField(
            'authentication',
            'select',
            array(
                'label' => __('Authentication'),
                'name' => 'authentication',
                'required' => true,
                'disabled' => false,
                'options' => array(
                    \Magento\Integration\Model\Integration::AUTHENTICATION_OAUTH => __('OAuth'),
                    \Magento\Integration\Model\Integration::AUTHENTICATION_MANUAL => __('Manual'),
                ),
            )
        );
        $fieldset->addField(
            'endpoint',
            'text',
            array(
                'label' => __('Endpoint URL'),
                'name' => 'endpoint',
                'required' => true,
                'disabled' => false
            )
        );
        $form->setValues($integrationData);
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
        return __('Integration Properties');
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
