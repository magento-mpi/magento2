<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Helper;

use Magento\Sales\Model\Quote;
use Magento\Store\Model\Store;
use Magento\Payment\Block\Form;
use Magento\Payment\Model\Info;
use Magento\View\Element\Template;

/**
 * Payment module base helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_PATH_PAYMENT_METHODS = 'payment';

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * Layout
     *
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Factory for payment method models
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_methodFactory;

    /**
     * App emulation model
     *
     * @var \Magento\Core\Model\App\Emulation
     */
    protected $_appEmulation;

    /**
     * @var \Magento\App\Config\Initial
     */
    protected $_initialConfig;

    /**
     * Construct
     *
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Payment\Model\Method\Factory $paymentMethodFactory
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\Core\Model\App\Emulation $appEmulation
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\App\Config\Initial $initialConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\View\LayoutInterface $layout,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Core\Model\App\Emulation $appEmulation,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\App\Config\Initial $initialConfig
    ) {
        parent::__construct($context);
        $this->_storeConfig = $coreStoreConfig;
        $this->_layout = $layout;
        $this->_methodFactory = $paymentMethodFactory;
        $this->_appEmulation = $appEmulation;
        $this->_paymentConfig = $paymentConfig;
        $this->_initialConfig = $initialConfig;
    }

    /**
     * Retrieve method model object
     *
     * @param string $code
     * @return \Magento\Payment\Model\MethodInterface|false
     */
    public function getMethodInstance($code)
    {
        $key = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/model';
        $class = $this->_storeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $class ? $this->_methodFactory->create($class) : false;
    }

    /**
     * Get and sort available payment methods for specified or current store
     *
     * Array structure:
     *  $index => \Magento\Simplexml\Element
     *
     * @param null|string|bool|int|Store $store
     * @param Quote|null $quote
     * @return array
     */
    public function getStoreMethods($store = null, $quote = null)
    {
        $res = array();
        $methods = $this->getPaymentMethods();
        uasort($methods, array($this, '_sortMethods'));
        foreach ($methods as $code => $methodConfig) {
            $prefix = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
            if (!($model = $this->_storeConfig->getValue(
                $prefix . 'model',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            ))
            ) {
                continue;
            }
            $methodInstance = $this->_methodFactory->create($model);
            if (!$methodInstance) {
                continue;
            }
            $methodInstance->setStore($store);
            if (!$methodInstance->isAvailable($quote)) {
                /* if the payment method cannot be used at this time */
                continue;
            }
            $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
            $methodInstance->setSortOrder($sortOrder);
            $res[] = $methodInstance;
        }

        return $res;
    }

    /**
     * @param $mixed $a
     * @param $mixed $b
     * @return int
     */
    protected function _sortMethods($a, $b)
    {
        if (is_object($a)) {
            return (int)$a->sort_order <
                (int)$b->sort_order ? -1 : ((int)$a->sort_order >
                (int)$b->sort_order ? 1 : 0);
        }
        return 0;
    }

    /**
     * Retrieve payment method form html
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return Form
     */
    public function getMethodFormBlock(\Magento\Payment\Model\MethodInterface $method)
    {
        $block = false;
        $blockType = $method->getFormBlockType();
        if ($this->_layout) {
            $block = $this->_layout->createBlock($blockType, $method->getCode());
            $block->setMethod($method);
        }
        return $block;
    }

    /**
     * Retrieve payment information block
     *
     * @param Info $info
     * @return Template
     */
    public function getInfoBlock(Info $info)
    {
        $blockType = $info->getMethodInstance()->getInfoBlockType();
        $block = $this->_layout->createBlock($blockType);
        $block->setInfo($info);
        return $block;
    }

    /**
     * Render payment information block
     *
     * @param Info $info
     * @param int $storeId
     * @return string
     * @throws \Exception
     */
    public function getInfoBlockHtml(Info $info, $storeId)
    {
        $initialEnvironmentInfo = $this->_appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = $info->getBlockMock() ?: $this->getInfoBlock($info);
            $paymentBlock->setArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (\Exception $exception) {
            $this->_appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        $this->_appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $paymentBlockHtml;
    }

    /**
     * Retrieve available billing agreement methods
     *
     * @param mixed $store
     * @param \Magento\Sales\Model\Quote $quote
     * @return array
     */
    public function getBillingAgreementMethods($store = null, $quote = null)
    {
        $result = array();
        foreach ($this->getStoreMethods($store, $quote) as $method) {
            if ($method->canManageBillingAgreements()) {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * Retrieve all payment methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        return $this->_initialConfig->getData('default')[self::XML_PATH_PAYMENT_METHODS];
    }

    /**
     * Retrieve all payment methods list as an array
     *
     * Possible output:
     * 1) assoc array as <code> => <title>
     * 2) array of array('label' => <title>, 'value' => <code>)
     * 3) array of array(
     *                 array('value' => <code>, 'label' => <title>),
     *                 array('value' => array(
     *                     'value' => array(array(<code1> => <title1>, <code2> =>...),
     *                     'label' => <group name>
     *                 )),
     *                 array('value' => <code>, 'label' => <title>),
     *                 ...
     *             )
     *
     * @param bool $sorted
     * @param bool $asLabelValue
     * @param bool $withGroups
     * @param Store|null $store
     * @return array
     */
    public function getPaymentMethodList($sorted = true, $asLabelValue = false, $withGroups = false, $store = null)
    {
        $methods = array();
        $groups = array();
        $groupRelations = array();

        foreach ($this->getPaymentMethods() as $code => $data) {
            if (isset($data['title'])) {
                $methods[$code] = $data['title'];
            } else {
                if ($this->getMethodInstance($code)) {
                    $methods[$code] = $this->getMethodInstance($code)->getConfigData('title', $store);
                }
            }
            if ($asLabelValue && $withGroups && isset($data['group'])) {
                $groupRelations[$code] = $data['group'];
            }
        }
        if ($asLabelValue && $withGroups) {
            $groups = $this->_paymentConfig->getGroups();
            foreach ($groups as $code => $title) {
                $methods[$code] = $title;
            }
        }
        if ($sorted) {
            asort($methods);
        }
        if ($asLabelValue) {
            $labelValues = array();
            foreach ($methods as $code => $title) {
                $labelValues[$code] = array();
            }
            foreach ($methods as $code => $title) {
                if (isset($groups[$code])) {
                    $labelValues[$code]['label'] = $title;
                } elseif (isset($groupRelations[$code])) {
                    unset($labelValues[$code]);
                    $labelValues[$groupRelations[$code]]['value'][$code] = array('value' => $code, 'label' => $title);
                } else {
                    $labelValues[$code] = array('value' => $code, 'label' => $title);
                }
            }
            return $labelValues;
        }

        return $methods;
    }

    /**
     * Returns value of Zero Subtotal Checkout / Enabled
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function isZeroSubTotal($store = null)
    {
        return $this->_storeConfig->getValue(
            \Magento\Payment\Model\Method\Free::XML_PATH_PAYMENT_FREE_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns value of Zero Subtotal Checkout / New Order Status
     *
     * @param null|string|bool|int|Store $store
     * @return string
     */
    public function getZeroSubTotalOrderStatus($store = null)
    {
        return $this->_storeConfig->getValue(
            \Magento\Payment\Model\Method\Free::XML_PATH_PAYMENT_FREE_ORDER_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns value of Zero Subtotal Checkout / Automatically Invoice All Items
     *
     * @param null|string|bool|int|Store $store
     * @return string
     */
    public function getZeroSubTotalPaymentAutomaticInvoice($store = null)
    {
        return $this->_storeConfig->getValue(
            \Magento\Payment\Model\Method\Free::XML_PATH_PAYMENT_FREE_PAYMENT_ACTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
