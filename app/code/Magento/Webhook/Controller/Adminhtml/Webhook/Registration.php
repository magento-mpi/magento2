<?php
/**
 * Registration controller
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Magento_Webhook_Controller_Adminhtml_Webhook_Registration extends Magento_Backend_Controller_ActionAbstract
{
    const DATA_SUBSCRIPTION_ID = 'subscription_id';
    const DATA_TOPICS = 'topics';
    const DATA_NAME = 'name';

    /** Key used to store subscription data into the registry */
    const REGISTRY_KEY_CURRENT_SUBSCRIPTION = 'current_subscription';

    /** Param keys used to extract subscription details from the Request */
    const PARAM_SUBSCRIPTION_ID = 'id';
    const PARAM_APIKEY = 'apikey';
    const PARAM_APISECRET = 'apisecret';
    const PARAM_EMAIL = 'email';
    const PARAM_COMPANY = 'company';

    /** @var Magento_Core_Model_Registry */
    private $_registry;

<<<<<<< HEAD:app/code/Mage/Webhook/controllers/Adminhtml/Webhook/RegistrationController.php
    /** @var Mage_Webhook_Service_SubscriptionInterfaceV1 */
=======
    /** @var Magento_Webhook_Service_SubscriptionV1Interface */
>>>>>>> upstream/develop:app/code/Magento/Webhook/Controller/Adminhtml/Webhook/Registration.php
    private $_subscriptionService;

    /** @var Magento_Webhook_Model_Webapi_User_Factory */
    private $_userFactory;


    /**
<<<<<<< HEAD:app/code/Mage/Webhook/controllers/Adminhtml/Webhook/RegistrationController.php
     * @param Mage_Webhook_Model_Webapi_User_Factory $userFactory
     * @param Mage_Webhook_Service_SubscriptionInterfaceV1 $subscriptionService
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Backend_Controller_Context $context
     * @param string $areaCode
     */
    public function __construct(
        Mage_Webhook_Model_Webapi_User_Factory $userFactory,
        Mage_Webhook_Service_SubscriptionInterfaceV1 $subscriptionService,
        Mage_Core_Model_Registry $registry,
        Mage_Backend_Controller_Context $context,
=======
     * @param Magento_Webhook_Model_Webapi_User_Factory $userFactory
     * @param Magento_Webhook_Service_SubscriptionV1Interface $subscriptionService
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Backend_Controller_Context $context
     * @param string $areaCode
     */
    public function __construct(
        Magento_Webhook_Model_Webapi_User_Factory $userFactory,
        Magento_Webhook_Service_SubscriptionV1Interface $subscriptionService,
        Magento_Core_Model_Registry $registry,
        Magento_Backend_Controller_Context $context,
>>>>>>> upstream/develop:app/code/Magento/Webhook/Controller/Adminhtml/Webhook/Registration.php
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_userFactory = $userFactory;
        $this->_subscriptionService = $subscriptionService;
        $this->_registry = $registry;
    }

    /**
     * Activate subscription
     * Step 1 - display subscription required resources
     */
    public function activateAction()
    {
        try {
            $this->_initSubscription();
            $this->loadLayout();
            $this->renderLayout();
        } catch (Magento_Core_Exception $e) {
            $this->_redirectFailed($e->getMessage());
        }
    }

    /**
     * Agree to provide required subscription resources
     * Step 2 - redirect to specified auth action
     */
    public function acceptAction()
    {
        try {
            $subscriptionData = $this->_initSubscription();

            $route = '*/webhook_registration/user';
            $this->_redirect(
                $route,
                array(self::PARAM_SUBSCRIPTION_ID => $subscriptionData[self::DATA_SUBSCRIPTION_ID])
            );
        } catch (Magento_Core_Exception $e) {
            $this->_redirectFailed($e->getMessage());
        }
    }

    /**
     * Displays form for gathering api user data
     */
    public function userAction()
    {
        try {
            $this->_initSubscription();
            $this->loadLayout();
            $this->renderLayout();
        } catch (Magento_Core_Exception $e) {
            $this->_redirectFailed($e->getMessage());
        }
    }

    /**
     * Continue createApiUser
     */
    public function registerAction()
    {
        try {
            $subscriptionData = $this->_initSubscription();
            /** @var string $key */
            $key = $this->getRequest()->getParam(self::PARAM_APIKEY);
            /** @var string $secret */
            $secret = $this->getRequest()->getParam(self::PARAM_APISECRET);
            /** @var string $email */
            $email = $this->getRequest()->getParam(self::PARAM_EMAIL);
            /** @var string $company */
            $company = $this->getRequest()->getParam(self::PARAM_COMPANY);

            if (empty($key) || empty($secret) || empty($email)) {
                throw new Magento_Webhook_Exception(
                    __('API Key, API Secret and Contact Email are required fields.')
                );
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->_redirectFailed(__('Invalid Email address provided'));
                return;
            }

            $userContext = array(
                'email' => $email,
                'key'       => $key,
                'secret'    => $secret,
                'company' => $company,
            );

            /** @var string[] $topics */
            $topics = $subscriptionData[self::DATA_TOPICS];
            $userId = $this->_userFactory->createUser($userContext, $topics);

            $subscriptionData['api_user_id'] = $userId;
            $subscriptionData['status'] = Magento_Webhook_Model_Subscription::STATUS_ACTIVE;
            $subscriptionData = $this->_subscriptionService->update($subscriptionData);

            $this->_redirectSucceeded($subscriptionData);

        } catch (Magento_Core_Exception $e) {
            $this->_redirectFailed($e->getMessage());
        }
    }

    /**
     * Redirect to this page when the authentication process is completed successfully
     */
    public function succeededAction()
    {
        try {
            $this->loadLayout();
            $this->renderLayout();
            $subscriptionData = $this->_initSubscription();

            $this->_getSession()->addSuccess(
                __('The subscription \'%1\' has been activated.',
                    $subscriptionData[self::DATA_NAME])
            );
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
    }

    /**
     * Redirect to this action when the authentication process fails for any reason.
     */
    public function failedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Initialize general settings for subscription
     *
     * @throws Exception|Magento_Core_Exception if subscription can't be found
     * @return array
     */
    protected function _initSubscription()
    {
        $subscriptionId = (int) $this->getRequest()->getParam(self::PARAM_SUBSCRIPTION_ID);
        $subscriptionData = $this->_subscriptionService->get($subscriptionId);

        $this->_registry->register(self::REGISTRY_KEY_CURRENT_SUBSCRIPTION, $subscriptionData);
        return $subscriptionData;
    }

    /**
     * Log successful subscription and redirect to success page
     *
     * @param array $subscriptionData
     */
    protected function _redirectSucceeded(array $subscriptionData)
    {
        $this->_getSession()->addSuccess(
            __('The subscription \'%1\' has been activated.', $subscriptionData[self::DATA_NAME])
        );
        $this->_redirect('*/webhook_registration/succeeded',
            array(self::PARAM_SUBSCRIPTION_ID => $subscriptionData[self::DATA_SUBSCRIPTION_ID]));
    }

    /**
     * Add error and redirect to failure page
     *
     * @param string $errorMessage
     */
    protected function _redirectFailed($errorMessage)
    {
        $this->_getSession()->addError($errorMessage);
        $this->_redirect('*/webhook_registration/failed');
    }
}
