<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\AdminNotification\Model\System\Message;

class Baseurl implements \Magento\AdminNotification\Model\System\MessageInterface
{
    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\Core\Model\Config\ValueFactory $configValueFactory
     */
    public function __construct(
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\Core\Model\Config\ValueFactory $configValueFactory
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_configValueFactory = $configValueFactory;
    }

    /**
     * Get url for config settings where base url option can be changed
     *
     * @return string
     */
    protected function _getConfigUrl()
    {
        $output = '';
        $defaultUnsecure = $this->_config->getValue(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL, 'default');

        $defaultSecure = $this->_config->getValue(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL, 'default');

        if ($defaultSecure == \Magento\Core\Model\Store::BASE_URL_PLACEHOLDER ||
            $defaultUnsecure == \Magento\Core\Model\Store::BASE_URL_PLACEHOLDER
        ) {
            $output = $this->_urlBuilder->getUrl('adminhtml/system_config/edit', array('section' => 'web'));
        } else {
            /** @var $dataCollection \Magento\Core\Model\Resource\Config\Data\Collection */
            $dataCollection = $this->_configValueFactory->create()->getCollection();
            $dataCollection->addValueFilter(\Magento\Core\Model\Store::BASE_URL_PLACEHOLDER);

            /** @var $data \Magento\App\Config\ValueInterface */
            foreach ($dataCollection as $data) {
                if ($data->getScope() == 'stores') {
                    $code = $this->_storeManager->getStore($data->getScopeId())->getCode();
                    $output = $this->_urlBuilder->getUrl(
                        'adminhtml/system_config/edit',
                        array('section' => 'web', 'store' => $code)
                    );
                    break;
                } elseif ($data->getScope() == 'websites') {
                    $code = $this->_storeManager->getWebsite($data->getScopeId())->getCode();
                    $output = $this->_urlBuilder->getUrl(
                        'adminhtml/system_config/edit',
                        array('section' => 'web', 'website' => $code)
                    );
                    break;
                }
            }
        }
        return $output;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('BASE_URL' . $this->_getConfigUrl());
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return (bool)$this->_getConfigUrl();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return __(
            '{{base_url}} is not recommended to use in a production environment to declare the Base Unsecure URL / Base Secure URL. It is highly recommended to change this value in your Magento <a href="%1">configuration</a>.',
            $this->_getConfigUrl()
        );
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
