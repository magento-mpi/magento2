<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * OAuth consumer edit form block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Consumer model
     *
     * @var Magento_Oauth_Model_Consumer
     */
    protected $_model;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $formFactory, $data);
    }

    /**
     * Get consumer model
     *
     * @return Magento_Oauth_Model_Consumer
     */
    public function getModel()
    {
        if (null === $this->_model) {
            $this->_model = $this->_coreRegistry->registry('current_consumer');
        }
        return $this->_model;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = $this->_createForm(array(
            'id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Consumer Information'), 'class' => 'fieldset-wide'
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id', 'value' => $model->getId()));
        }
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => __('Name'),
            'title'     => __('Name'),
            'required'  => true,
            'value'     => $model->getName(),
        ));

        $fieldset->addField('key', 'text', array(
            'name'      => 'key',
            'label'     => __('Key'),
            'title'     => __('Key'),
            'disabled'  => true,
            'required'  => true,
            'value'     => $model->getKey(),
        ));

        $fieldset->addField('secret', 'text', array(
            'name'      => 'secret',
            'label'     => __('Secret'),
            'title'     => __('Secret'),
            'disabled'  => true,
            'required'  => true,
            'value'     => $model->getSecret(),
        ));

        $fieldset->addField('callback_url', 'text', array(
            'name'      => 'callback_url',
            'label'     => __('Callback URL'),
            'title'     => __('Callback URL'),
            'required'  => false,
            'value'     => $model->getCallbackUrl(),
            'class'     => 'validate-url',
        ));

        $fieldset->addField('rejected_callback_url', 'text', array(
            'name'      => 'rejected_callback_url',
            'label'     => __('Rejected Callback URL'),
            'title'     => __('Rejected Callback URL'),
            'required'  => false,
            'value'     => $model->getRejectedCallbackUrl(),
            'class'     => 'validate-url',
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
