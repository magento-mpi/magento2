<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper;

/**
 * Customer Data Helper
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Customer group cache context
     */
    const CONTEXT_GROUP = 'customer_group';

    /**
     * Customer authorization cache context
     */
    const CONTEXT_AUTH = 'customer_logged_in';

    /**
     * Query param name for last url visited
     */
    const REFERER_QUERY_PARAM_NAME = 'referer';

    /**
     * Route for customer account login page
     */
    const ROUTE_ACCOUNT_LOGIN = 'customer/account/login';

    /**
     * Config name for Redirect Customer to Account Dashboard after Logging in setting
     */
    const XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD = 'customer/startup/redirect_dashboard';

    /**
     * Config paths to VAT related customer groups
     */
    const XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP = 'customer/create_account/viv_intra_union_group';

    const XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP = 'customer/create_account/viv_domestic_group';

    const XML_PATH_CUSTOMER_VIV_INVALID_GROUP = 'customer/create_account/viv_invalid_group';

    const XML_PATH_CUSTOMER_VIV_ERROR_GROUP = 'customer/create_account/viv_error_group';

    /**
     * Config path to option that enables/disables automatic group assignment based on VAT
     */
    const XML_PATH_CUSTOMER_GROUP_AUTO_ASSIGN = 'customer/create_account/auto_group_assign';

    /**
     * Config path to support email
     */
    const XML_PATH_SUPPORT_EMAIL = 'trans_email/ident_support/email';

    /**
     * WSDL of VAT validation service
     *
     */
    const VAT_VALIDATION_WSDL_URL = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService?wsdl';

    /**
     * Configuration path to expiration period of reset password link
     */
    const XML_PATH_CUSTOMER_RESET_PASSWORD_LINK_EXPIRATION_PERIOD = 'customer/password/reset_link_expiration_period';

    /**
     * Configuration path to merchant country id
     */
    const XML_PATH_MERCHANT_COUNTRY_CODE = 'general/store_information/country_id';

    /**
     * Config path to merchant VAT number
     */
    const XML_PATH_MERCHANT_VAT_NUMBER = 'general/store_information/merchant_vat_number';

    /**
     * Config path to UE country list
     */
    const XML_PATH_EU_COUNTRIES_LIST = 'general/country/eu_countries';

    /**
     * VAT class constants
     */
    const VAT_CLASS_DOMESTIC = 'domestic';

    const VAT_CLASS_INTRA_UNION = 'intra_union';

    const VAT_CLASS_INVALID = 'invalid';

    const VAT_CLASS_ERROR = 'error';

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Customer address
     *
     * @var Address
     */
    protected $_customerAddress = null;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Address $customerAddress
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Math\Random $mathRandom
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Address $customerAddress,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Math\Random $mathRandom
    ) {
        $this->_customerAddress = $customerAddress;
        $this->_coreData = $coreData;
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_groupService = $groupService;
        $this->_formFactory = $formFactory;
        $this->_escaper = $escaper;
        $this->mathRandom = $mathRandom;
        parent::__construct($context);
    }

    /**
     * Check whether customers registration is allowed
     *
     * @return bool
     */
    public function isRegistrationAllowed()
    {
        return true;
    }

    /**
     * Retrieve name prefix dropdown options
     *
     * @param null $store
     * @return array|bool
     */
    public function getNamePrefixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions($this->_customerAddress->getConfig('prefix_options', $store));
    }

    /**
     * Retrieve name suffix dropdown options
     *
     * @param null $store
     * @return array|bool
     */
    public function getNameSuffixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions($this->_customerAddress->getConfig('suffix_options', $store));
    }

    /**
     * Unserialize and clear name prefix or suffix options
     *
     * @param string $options
     * @return array|bool
     */
    protected function _prepareNamePrefixSuffixOptions($options)
    {
        $options = trim($options);
        if (empty($options)) {
            return false;
        }
        $result = array();
        $options = explode(';', $options);
        foreach ($options as $value) {
            $value = $this->_escaper->escapeHtml(trim($value));
            $result[$value] = $value;
        }
        return $result;
    }

    /**
     * Perform customer data filtration based on form code and form object
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $formCode The code of EAV form to take the list of attributes from
     * @param string $entityType entity type for the form
     * @param string[] $additionalAttributes The list of attribute codes to skip filtration for
     * @param string $scope scope of the request
     * @param \Magento\Customer\Model\Metadata\Form $metadataForm to use for extraction
     * @return array Filtered customer data
     */
    public function extractCustomerData(
        \Magento\Framework\App\RequestInterface $request,
        $formCode,
        $entityType,
        $additionalAttributes = array(),
        $scope = null,
        \Magento\Customer\Model\Metadata\Form $metadataForm = null
    ) {
        if (is_null($metadataForm)) {
            $metadataForm = $this->_formFactory->create(
                $entityType,
                $formCode,
                array(),
                false,
                \Magento\Customer\Model\Metadata\Form::DONT_IGNORE_INVISIBLE
            );
        }
        $filteredData = $metadataForm->extractData($request, $scope);
        $requestData = $request->getPost($scope);
        foreach ($additionalAttributes as $attributeCode) {
            $filteredData[$attributeCode] = isset($requestData[$attributeCode]) ? $requestData[$attributeCode] : false;
        }

        $formAttributes = $metadataForm->getAttributes();
        /** @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute */
        foreach ($formAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $frontendInput = $attribute->getFrontendInput();
            if ($frontendInput != 'boolean' && $filteredData[$attributeCode] === false) {
                unset($filteredData[$attributeCode]);
            }
        }

        return $filteredData;
    }
}
