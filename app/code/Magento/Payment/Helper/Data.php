<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment module base helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Payment\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_PAYMENT_METHODS = 'payment';
    const XML_PATH_PAYMENT_GROUPS = 'global/payment/groups';

    /**
     * Retrieve method model object
     *
     * @param   string $code
     * @return  \Magento\Payment\Model\Method\AbstractMethod|false
     */
    public function getMethodInstance($code)
    {
        $key = self::XML_PATH_PAYMENT_METHODS.'/'.$code.'/model';
        $class = \Mage::getStoreConfig($key);
        return \Mage::getModel($class);
    }

    /**
     * Get and sort available payment methods for specified or current store
     *
     * array structure:
     *  $index => \Magento\Simplexml\Element
     *
     * @param mixed $store
     * @param \Magento\Sales\Model\Quote $quote
     * @return array
     */
    public function getStoreMethods($store = null, $quote = null)
    {
        $res = array();
        $methods = $this->getPaymentMethods($store);
        uasort($methods, array($this, '_sortMethods'));
        foreach ($methods as $code => $methodConfig) {
            $prefix = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
            if (!$model = \Mage::getStoreConfig($prefix . 'model', $store)) {
                continue;
            }
            $methodInstance = \Mage::getModel($model);
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

    protected function _sortMethods($a, $b)
    {
        if (is_object($a)) {
            return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);
        }
        return 0;
    }

    /**
     * Retreive payment method form html
     *
     * @param   Magento_Payment_Model_Abstract $method
     * @return  \Magento\Payment\Block\Form
     */
    public function getMethodFormBlock(\Magento\Payment\Model\Method\AbstractMethod $method)
    {
        $block = false;
        $blockType = $method->getFormBlockType();
        if ($this->getLayout()) {
            $block = $this->getLayout()->createBlock($blockType, $method->getCode());
            $block->setMethod($method);
        }
        return $block;
    }

    /**
     * Retrieve payment information block
     *
     * @param  \Magento\Payment\Model\Info $info
     * @return \Magento\Core\Block\Template
     */
    public function getInfoBlock(\Magento\Payment\Model\Info $info)
    {
        $blockType = $info->getMethodInstance()->getInfoBlockType();
        $layout = $this->getLayout() ?: \Mage::app()->getLayout();
        $block = $layout->createBlock($blockType);
        $block->setInfo($info);
        return $block;
    }

    /**
     * Render payment information block
     *
     * @param  \Magento\Payment\Model\Info $info
     * @param  int $storeId
     * @return string
     * @throws \Exception
     */
    public function getInfoBlockHtml(\Magento\Payment\Model\Info $info, $storeId)
    {
        /** @var $appEmulation \Magento\Core\Model\App\Emulation */
        $appEmulation = \Mage::getSingleton('Magento\Core\Model\App\Emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = $info->getBlockMock() ?: $this->getInfoBlock($info);
            $paymentBlock->setArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (\Exception $exception) {
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

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
     * Get payment methods that implement recurring profilez management
     *
     * @param mixed $store
     * @return array
     */
    public function getRecurringProfileMethods($store = null)
    {
        $result = array();
        foreach ($this->getPaymentMethods($store) as $code => $data) {
            $method = $this->getMethodInstance($code);
            if ($method && $method->canManageRecurringProfiles()) {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * Retrieve all payment methods
     *
     * @param mixed $store
     * @return array
     */
    public function getPaymentMethods($store = null)
    {
        return \Mage::getStoreConfig(self::XML_PATH_PAYMENT_METHODS, $store);
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
     * @return array
     */
    public function getPaymentMethodList($sorted = true, $asLabelValue = false, $withGroups = false, $store = null)
    {
        $methods = array();
        $groups = array();
        $groupRelations = array();

        foreach ($this->getPaymentMethods($store) as $code => $data) {
            if ((isset($data['title']))) {
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
            $groups = \Mage::app()->getConfig()->getNode(self::XML_PATH_PAYMENT_GROUPS)->asCanonicalArray();
            foreach ($groups as $code => $title) {
                $methods[$code] = $title; // for sorting, see below
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
     * Retrieve all billing agreement methods (code and label)
     *
     * @return array
     */
    public function getAllBillingAgreementMethods()
    {
        $result = array();
        $interface = 'Magento\Payment\Model\Billing\Agreement\MethodInterface';
        foreach ($this->getPaymentMethods() as $code => $data) {
            if (!isset($data['model'])) {
                continue;
            }
            $method = $data['model'];
            if (in_array($interface, class_implements($method))) {
                $result[$code] = $data['title'];
            }
        }
        return $result;
    }

    /**
     * Returns value of Zero Subtotal Checkout / Enabled
     *
     * @param mixed $store
     * @return boolean
     */
    public function isZeroSubTotal($store = null)
    {
        return \Mage::getStoreConfig(\Magento\Payment\Model\Method\Free::XML_PATH_PAYMENT_FREE_ACTIVE, $store);
    }

    /**
     * Returns value of Zero Subtotal Checkout / New Order Status
     *
     * @param mixed $store
     * @return string
     */
    public function getZeroSubTotalOrderStatus($store = null)
    {
        return \Mage::getStoreConfig(\Magento\Payment\Model\Method\Free::XML_PATH_PAYMENT_FREE_ORDER_STATUS, $store);
    }

    /**
     * Returns value of Zero Subtotal Checkout / Automatically Invoice All Items
     *
     * @param mixed $store
     * @return string
     */
    public function getZeroSubTotalPaymentAutomaticInvoice($store = null)
    {
        return \Mage::getStoreConfig(\Magento\Payment\Model\Method\Free::XML_PATH_PAYMENT_FREE_PAYMENT_ACTION, $store);
    }
}
