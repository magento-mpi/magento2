<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Block\Iframe;

/**
 * Abstract payment block
 */
abstract class AbstractIframe extends \Magento\Payment\Block\Form
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '360';

    /**
     * Default iframe height for 3D Secure authorization
     *
     * @var string
     */
    protected $_iframeHeight3dSecure = '425';

    /**
     * Default iframe block type
     *
     * @var string
     */
    protected $_iframeBlockType = 'Magento\Framework\View\Element\Template';

    /**
     * Default iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Magento_Pbridge::iframe.phtml';

    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData = null;

    /**
     * Region factory
     *
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * Pbridge session
     *
     * @var \Magento\Pbridge\Model\Session
     */
    protected $_pbridgeSession;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Pbridge\Model\Session $pbridgeSession,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_pbridgeSession = $pbridgeSession;
        $this->_regionFactory = $regionFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('magento_pbridge/pbridge/result', ['_current' => true, '_secure' => true]);
    }

    /**
     * Getter
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Getter for $_iframeHeight
     *
     * @return string
     */
    public function getIframeHeight()
    {
        return $this->_iframeHeight;
    }

    /**
     * Getter.
     * Return Payment Bridge url with required parameters (such as merchant code, merchant key etc.)
     *
     * @return string
     */
    abstract public function getSourceUrl();

    /**
     * Create default billing address request data
     *
     * @return array
     */
    protected function _getAddressInfo()
    {
        $address = $this->_getCurrentCustomer()->getDefaultBilling();

        $addressFileds = [
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
            'company',
            'city',
            'country_id',
            'telephone',
            'fax',
            'postcode'
        ];

        $result = [];
        if ($address) {
            foreach ($addressFileds as $addressField) {
                if ($address->hasData($addressField)) {
                    $result[$addressField] = $address->getData($addressField);
                }
            }
            //Streets must be transfered separately
            $streets = $address->getStreet();
            $result['street'] = array_shift($streets);
            $street2 = array_shift($streets);
            if ($street2) {
                $result['street2'] = $street2;
            }
            //Region code lookup
            $region = $this->_regionFactory->create()->load($address->getData('region_id'));
            if ($region && $region->getId()) {
                $result['region'] = $region->getCode();
            }
        }
        return $result;
    }

    /**
     * Create and return iframe block
     *
     * @return \Magento\Framework\View\Element\Template
     */
    public function getIframeBlock()
    {
        $iframeBlock = $this->getLayout()->createBlock($this->_iframeBlockType)
            ->setTemplate($this->_iframeTemplate)
            ->setIframeHeight($this->getIframeHeight())
            ->setSourceUrl($this->getSourceUrl());
        return $iframeBlock;
    }

    /**
     * Returns config options for PBridge iframe block
     *
     * @param string $param
     * @return string
     */
    public function getFrameParam($param = '')
    {
        return $this->_scopeConfig->getValue('payment_services/pbridge_styling/' . $param, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setChild('pbridge_iframe', $this->getIframeBlock());
        return parent::_toHtml();
    }

    /**
     * Returns merged css url for pbridge
     *
     * @return string
     */
    public function getCssUrl()
    {
        if (!$this->getFrameParam('use_theme')) {
            return '';
        }
        return '';
    }

    /**
     * Merge css array into one url
     *
     * @param array $staticItems
     * @param array $skinItems
     * @param null $mergeCallback
     * @return string
     */
    protected function _prepareCssElements(array $staticItems, array $skinItems, $mergeCallback = null)
    {
        $baseJsUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_JS);
        $items = [];
        if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
        }

        // get static files from the js folder, no need in lookups
        foreach ($staticItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $this->_dirs->getPath() . '/js/' . $name : $baseJsUrl . $name;
            }
        }

        // lookup each file basing on current theme configuration
        foreach ($skinItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $this->_assetRepo->createAsset($name)->getSourceFile()
                    : $this->_assetRepo->getUrl($name);
            }
        }

        foreach ($items as $params => $rows) {
            // attempt to merge
            $mergedUrl = false;
            if ($mergeCallback) {
                $mergedUrl = call_user_func($mergeCallback, $rows);
            }

            if ($mergedUrl) {
                $url[] = $mergedUrl;
            } else {
                foreach ($rows as $src) {
                    $url[] = $src;
                }
            }
        }
        return $url[0];
    }

    /**
     * Generate unique identifier for current merchant and customer
     *
     * @return null|string
     */
    public function getCustomerIdentifier()
    {
        $customer = $this->_getCurrentCustomer();
        $store = $this->_getCurrentStore();
        if ($customer && $customer->getEmail()) {
            return $this->_pbridgeData->getCustomerIdentifierByEmail($customer->getId(), $store->getId());
        }
        return null;
    }

    /**
     * Return current merchant and customer email
     *
     * @return null|string
     */
    public function getCustomerEmail()
    {
        $customer = $this->_getCurrentCustomer();
        $quote = $this->getQuote();
        if ($customer && $customer->getEmail()) {
            return $customer->getEmail();
        } elseif ($quote && $quote->getCustomerEmail()) {
            return $quote->getCustomerEmail();
        }
        return null;
    }

    /**
     * Return current merchant and customer name
     *
     *
     * @internal param $storeId
     * @return null|string
     */
    public function getCustomerName()
    {
        $customer = $this->_getCurrentCustomer();
        if ($customer && $customer->getFirstname()) {
            return $customer->getFirstname();
        }
        return null;
    }

    /**
     * Get current customer object
     *
     * @return null|\Magento\Customer\Api\Data\CustomerInterface
     */
    protected function _getCurrentCustomer()
    {
        if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
            return $this->_customerSession->getCustomer();
        }

        return null;
    }

    /**
     * Return store for current context
     *
     * @return \Magento\Store\Model\Store
     */
    protected function _getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }
}
