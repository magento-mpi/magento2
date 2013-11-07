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
 * Main Integration info edit form
 *
 * @category   Magento
 * @package    Magento_Integration
 */
class Info extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**#@+
     * edit_form element names.
     */
    const HTML_ID_PREFIX = 'integration_properties_';
    const DATA_ID = 'integration_id';
    const DATA_NAME = 'name';
    const DATA_EMAIL = 'email';
    const DATA_ENDPOINT = 'endpoint';
    /**#@-*/

    /**
     * Construct
     *
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
     * Set form id prefix, declare fields for integration info
     *
     * @return \Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix(self::HTML_ID_PREFIX);
        $integrationData = $this->_coreRegistry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION);
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Integration')));
        if (isset($integrationData[self::DATA_ID])) {
            $fieldset->addField(self::DATA_ID, 'hidden', array('name' => 'id'));
        }
        $fieldset->addField(
            self::DATA_NAME,
            'text',
            array(
                'label' => __('Integration Name'),
                'name' => self::DATA_NAME,
                'required' => true,
                'disabled' => false,
                'maxlength' => '255'
            )
        );
        $fieldset->addField(
            self::DATA_EMAIL,
            'text',
            array(
                'label' => __('Email'),
                'name' => self::DATA_EMAIL,
                'required' => true,
                'disabled' => false,
                'class' => 'validate-email',
                'maxlength' => '254',
            )
        );
        $fieldset->addField(
            self::DATA_ENDPOINT,
            'text',
            array(
                'label' => __('Callback URL'),
                'name' => self::DATA_ENDPOINT,
                'disabled' => false,
                'note'=> __('When using Oauth for token exchange, enter URL where Oauth credentials can be POST-ed.'),
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
        return __('Integration Info');
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
