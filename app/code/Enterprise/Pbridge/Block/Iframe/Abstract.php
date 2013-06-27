<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Pbridge_Block_Iframe_Abstract extends Mage_Payment_Block_Form
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '360';

    /**
     * Default iframe width
     *
     * @var string
     */
    protected $_iframeWidth = '100%';

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
    protected $_iframeBlockType = 'Mage_Core_Block_Template';

    /**
     * Default iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Enterprise_Pbridge::iframe.phtml';

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('enterprise_pbridge/pbridge/result', array('_current' => true, '_secure' => true));
    }

    /**
     * Getter
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote();
    }

    /**
     * Getter for $_iframeHeight
     * @return string
     */
    public function getIframeHeight()
    {
        return $this->_iframeHeight;
    }

    /**
     * Getter for $_iframeWidth
     *
     * @return string
     */
    public function getIframeWidth()
    {
        return $this->_iframeWidth;
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
            $region = Mage::getModel('Mage_Directory_Model_Region')->load($address->getData('region_id'));
            if ($region && $region->getId()) {
                $result['region'] = $region->getCode();
            }
        }
        return $result;
    }

    /**
     * Create and return iframe block
     *
     * @return Mage_Core_Block_Template
     */
    public function getIframeBlock()
    {
        $iframeBlock = $this->getLayout()->createBlock($this->_iframeBlockType)
            ->setTemplate($this->_iframeTemplate)
            ->setIframeHeight($this->getIframeHeight())
            ->setIframeWidth($this->getIframeWidth())
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
        return Mage::getStoreConfig('payment_services/pbridge_styling/' . $param);
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
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        if (!is_object($this->getLayout()->getBlock('head'))) {
            return Mage::getSingleton('Enterprise_Pbridge_Model_Session')->getCssUrl();
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
                $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
            );
        }
        Mage::getSingleton('Enterprise_Pbridge_Model_Session')->setCssUrl($url);
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
        $baseJsUrl = Mage::getBaseUrl('js');
        $items = array();
        if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
        }

        // get static files from the js folder, no need in lookups
        foreach ($staticItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? Mage::getBaseDir() . DS . 'js' . DS . $name : $baseJsUrl . $name;
            }
        }

        // lookup each file basing on current theme configuration
        foreach ($skinItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $mergeCallback ? $this->_viewFileSystem->getFilename($name, array('_type' => 'skin'))
                    : $this->_viewUrl->getViewFileUrl($name, array());
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
     *
     * @internal param $storeId
     * @return null|string
     */
    public function getCustomerIdentifier()
    {
        $customer = $this->_getCurrentCustomer();
        $store = $this->_getCurrentStore();
        if ($customer && $customer->getEmail()) {
            return Mage::helper('Enterprise_Pbridge_Helper_Data')->getCustomerIdentifierByEmail($customer->getEmail());
        }
        return null;
    }

    /**
     * Return current merchant and customer email
     *
     *
     * @internal param $storeId
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
        if ($customer && $customer->getName()) {
            return $customer->getName();
        }
        return null;
    }

    /**
     * Get current customer object
     *
     * @return null|Mage_Customer_Model_Customer
     */
    protected function _getCurrentCustomer()
    {
        if (Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn()) {
            return Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
        }

        return null;
    }

    /**
     * Return store for current context
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getCurrentStore()
    {
        return Mage::app()->getStore();
    }
}
