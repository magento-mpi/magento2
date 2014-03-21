<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model;

use Magento\Store\Model\Store;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Payment configuration model
 *
 * Used for retrieving configuration data by payment models
 */
class Config
{
    /**
     * @var array
     */
    protected $_methods;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Config\DataInterface
     */
    protected $_dataStorage;

    /**
     * Locale model
     *
     * @var \Magento\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * Payment method factory
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_methodFactory;

    /**
     * Construct
     *
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\App\Config\ScopeConfigInterface $coreConfig
     * @param \Magento\Payment\Model\Method\Factory $paymentMethodFactory
     * @param \Magento\Locale\ListsInterface $localeLists
     * @param \Magento\Config\DataInterface $dataStorage
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Locale\ListsInterface $localeLists,
        \Magento\Config\DataInterface $dataStorage
    ) {
        $this->_storeConfig = $coreStoreConfig;
        $this->_dataStorage = $dataStorage;
        $this->_coreConfig = $coreConfig;
        $this->_methodFactory = $paymentMethodFactory;
        $this->_localeLists = $localeLists;
    }

    /**
     * Retrieve active system payments
     *
     * @param null|string|bool|int|Store $store
     * @return array
     */
    public function getActiveMethods($store=null)
    {
        $methods = array();
        $config = $this->_storeConfig->getValue('payment', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
        foreach ($config as $code => $methodConfig) {
            if ($this->_storeConfig->isSetFlag('payment/'.$code.'/active', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store)) {
                if (array_key_exists('model', $methodConfig)) {
                    $methodModel = $this->_methodFactory->create($methodConfig['model']);
                    if ($methodModel && $methodModel->getConfigData('active', $store)) {
                        $methods[$code] = $this->_getMethod($code, $methodConfig);
                    }
                }
            }
        }
        return $methods;
    }

    /**
     * Retrieve all system payments
     *
     * @param null|string|bool|int|Store $store
     * @return array
     */
    public function getAllMethods($store=null)
    {
        $methods = array();
        $config = $this->_storeConfig->getValue('payment', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
        foreach ($config as $code => $methodConfig) {
            $data = $this->_getMethod($code, $methodConfig);
            if (false !== $data) {
                $methods[$code] = $data;
            }
        }
        return $methods;
    }

    /**
     * @param string $code
     * @param string $config
     * @param null|string|bool|int|Store $store
     * @return \Magento\Payment\Model\MethodInterface
     */
    protected function _getMethod($code, $config, $store = null)
    {
        if (isset($this->_methods[$code])) {
            return $this->_methods[$code];
        }
        if (empty($config['model'])) {
            return false;
        }
        $modelName = $config['model'];

        if (!class_exists($modelName)) {
            return false;
        }

        /** @var AbstractMethod $method */
        $method = $this->_methodFactory->create($modelName);
        $method->setId($code)->setStore($store);
        $this->_methods[$code] = $method;
        return $this->_methods[$code];
    }

    /**
     * Retrieve array of credit card types
     *
     * @return array
     */
    public function getCcTypes()
    {
        return $this->_dataStorage->get('credit_cards');
    }

    /**
     * Retrieve array of payment methods information
     *
     * @return array
     */
    public function getMethodsInfo()
    {
        return $this->_dataStorage->get('methods');
    }

    /**
     * Get payment groups
     *
     * @return array
     */
    public function getGroups()
    {
        $groups = $this->_dataStorage->get('groups');
        $result = array();
        foreach ($groups as $code => $title) {
            $result[$code] = $title;
        }
        return $result;
    }

    /**
     * Retrieve list of months translation
     *
     * @return array
     */
    public function getMonths()
    {
        $data = $this->_localeLists->getTranslationList('month');
        foreach ($data as $key => $value) {
            $monthNum = ($key < 10) ? '0'.$key : $key;
            $data[$key] = $monthNum . ' - ' . $value;
        }
        return $data;
    }

    /**
     * Retrieve array of available years
     *
     * @return array
     */
    public function getYears()
    {
        $years = array();
        $first = date("Y");

        for ($index=0; $index <= 10; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }
        return $years;
    }
}
