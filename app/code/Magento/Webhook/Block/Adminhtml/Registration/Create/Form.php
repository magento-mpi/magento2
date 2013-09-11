<?php
/**
 * Creates registration form
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration\Create;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /** Constants for API user details */
    const API_KEY_LENGTH = 32;
    const API_SECRET_LENGTH = 32;
    const MIN_TEXT_INPUT_LENGTH = 20;

    /** Registry key for getting subscription data */
    const REGISTRY_KEY_CURRENT_SUBSCRIPTION = 'current_subscription';

    /** Data key for getting subscription id out of subscription data */
    const DATA_SUBSCRIPTION_ID = 'subscription_id';

    /** @var \Magento\Data\Form\Factory */
    private $_formFactory;

    /** @var \Magento\Core\Helper\Data  */
    private $_coreHelper;

    /** @var \Magento\Core\Model\Registry  */
    private $_registry;

    /**
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Data\Form\Factory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\Core\Model\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Data\Form\Factory $formFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_formFactory = $formFactory;
        $this->_coreHelper = $coreHelper;
        $this->_registry = $registry;
    }

    /**
     * Prepares registration form
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $subscription = $this->_registry->registry(self::REGISTRY_KEY_CURRENT_SUBSCRIPTION);
        $apiKey = $this->_generateRandomString(self::API_KEY_LENGTH);
        $apiSecret = $this->_generateRandomString(self::API_SECRET_LENGTH);
        $inputLength = max(self::API_KEY_LENGTH, self::API_SECRET_LENGTH, self::MIN_TEXT_INPUT_LENGTH);

        $form = $this->_formFactory->create(array(
                'id' => 'api_user',
                'action' => $this->getUrl('*/*/register', array('id' => $subscription[self::DATA_SUBSCRIPTION_ID])),
                'method' => 'post',
            )
        );

        $fieldset = $form;

        $fieldset->addField('company', 'text', array(
            'label'     => __('Company'),
            'name'      => 'company',
            'size'      => $inputLength,
        ));

        $fieldset->addField('email', 'text', array(
            'label'     => __('Contact Email'),
            'name'      => 'email',
            'class'     => 'email',
            'required'  => true,
            'size'      => $inputLength,
        ));

        $fieldset->addField('apikey', 'text', array(
            'label'     => __('API Key'),
            'name'      => 'apikey',
            'value'     => $apiKey,
            'class'     => 'monospace',
            'required'  => true,
            'size'      => $inputLength,
        ));

        $fieldset->addField('apisecret', 'text', array(
            'label'     => __('API Secret'),
            'name'      => 'apisecret',
            'value'     => $apiSecret,
            'class'     => 'monospace',
            'required'  => true,
            'size'      => $inputLength,
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Generates a random alphanumeric string
     *
     * @param int $length
     * @return string
     */
    private function _generateRandomString($length)
    {
        return $this->_coreHelper
            ->getRandomString($length, \Magento\Core\Helper\Data::CHARS_DIGITS . \Magento\Core\Helper\Data::CHARS_LOWERS);
    }
}
