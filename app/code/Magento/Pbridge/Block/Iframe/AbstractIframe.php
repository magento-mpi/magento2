<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Iframe;

abstract class AbstractIframe extends \Magento\Payment\Block\Form
{
    /**
     * Default iframe width
     *
     * @var string
     */
    protected $_iframeWidth = '100%';

    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '350';

    /**
     * Default iframe block type
     *
     * @var string
     */
    protected $_iframeBlockType = 'Magento\View\Element\Template';

    /**
     * Default iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Magento_Pbridge::iframe.phtml';

    /**
     * Whether scrolling enabled for iframe element, auto or not
     *
     * @var string
     */
    protected $_iframeScrolling = 'auto';

    /**
     * Whether to allow iframe body reloading
     *
     * @var bool
     */
    protected $_allowReload = true;

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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Pbridge\Model\Session $pbridgeSession,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_customerSession = $customerSession;
        $this->_pbridgeSession = $pbridgeSession;
        $this->_regionFactory = $regionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('magento_pbridge/pbridge/result', array('_current' => true, '_secure' => true));
    }

    /**
     * Getter.
     * Return Payment Bridge url with required parameters (such as merchant code, merchant key etc.)
     *
     */
    abstract public function getSourceUrl();

    /**
     * Create default billing address request data
     *
     * @return array
     */
    protected function _getAddressInfo()
    {
        $address = $this->_getCurrentCustomer()->getDefaultBillingAddress();

        $addressFileds    = array(
            'prefix', 'firstname', 'middlename', 'lastname', 'suffix',
            'company', 'city', 'country_id', 'telephone', 'fax', 'postcode',
        );

        $result = array();
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
     * @return \Magento\View\Element\Template
     */
    public function getIframeBlock()
    {
        $iframeBlock = $this->getLayout()->createBlock($this->_iframeBlockType)
            ->setTemplate($this->_iframeTemplate)
            ->setScrolling($this->_iframeScrolling)
            ->setIframeWidth($this->_iframeWidth)
            ->setIframeHeight($this->_iframeHeight)
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
        return $this->_storeConfig->getConfig('payment_services/pbridge_styling/' . $param);
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
        $shouldMergeCss = $this->_storeConfig->getConfigFlag('dev/css/merge_css_files');
        if (!is_object($this->getLayout()->getBlock('head'))) {
            return $this->_pbridgeSession->getCssUrl();
        }
        $items = $this->getLayout()->getBlock('head')->getData('items');
        $lines  = array();
        foreach ($items as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            if (!empty($item['if'])) {
                continue;
            }
            if (strstr($item['params'], "all")) {
                if ($item['type'] == 'skin_css' || $item['type'] == 'js_css') {
                    $lines[$item['type']][$item['params']][$item['name']] = $item['name'];
                }
            }
        }
        if (!empty($lines)) {
            $url = $this->_prepareCssElements(
                empty($lines['js_css']) ? array() : $lines['js_css'],
                empty($lines['skin_css']) ? array() : $lines['skin_css'],
                $shouldMergeCss ? array($this->_design, 'getMergedCssUrl') : null
            );
        }
        $this->_pbridgeSession->setCssUrl($url);
        return $url;
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
        $baseJsUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Core\Model\Store::URL_TYPE_JS);
        $items = array();
        if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
        }

        // get static files from the js folder, no need in lookups
        foreach ($staticItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $this->_dirs->getDir() . '/js/' . $name : $baseJsUrl
                    . $name;
            }
        }

        // lookup each file basing on current theme configuration
        foreach ($skinItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $this->_viewFileSystem->getFilename($name)
                    : $this->_viewUrl->getViewFileUrl($name);
            }
        }

        foreach ($items as $params => $rows) {
            // attempt to merge
            $mergedUrl = false;
            if ($mergeCallback) {
                $mergedUrl = call_user_func($mergeCallback, $rows);
            }
            // render elements
            $params = trim($params);
            $params = $params ? ' ' . $params : '';
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
            return $this->_pbridgeData
                ->getCustomerIdentifierByEmail($customer->getId(), $store->getId());
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
        if ($customer && $customer->getEmail()) {
            return $customer->getEmail();
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
        if ($customer && $customer->getName()) {
            return $customer->getName();
        }
        return null;
    }

    /**
     * Get current customer object
     *
     * @return null|\Magento\Customer\Model\Customer
     */
    protected function _getCurrentCustomer()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer();
        }

        return null;
    }

    /**
     * Return store for current context
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }
}
