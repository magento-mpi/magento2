<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ogone\Model;

/**
 * Config model
 */
class Config extends \Magento\Payment\Model\Config
{
    const OGONE_PAYMENT_PATH = 'payment/ogone/';

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\App\Config\ScopeConfigInterface $coreConfig
     * @param \Magento\Payment\Model\Method\Factory $paymentMethodFactory
     * @param \Magento\Locale\ListsInterface $localeLists
     * @param \Magento\Config\DataInterface $dataStorage
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Locale\ListsInterface $localeLists,
        \Magento\Config\DataInterface $dataStorage,
        \Magento\UrlInterface $urlBuilder,
        \Magento\Encryption\EncryptorInterface $encryptor
    ) {
        parent::__construct($coreStoreConfig, $coreConfig, $paymentMethodFactory, $localeLists, $dataStorage);
        $this->_urlBuilder = $urlBuilder;
        $this->_encryptor = $encryptor;
    }

    /**
     * Return Ogone payment config information
     *
     * @param string $path
     * @param int|null $storeId
     * @return bool|null|string
     */
    public function getConfigData($path, $storeId=null)
    {
        if (!empty($path)) {
            return $this->_coreStoreConfig->getConfig(self::OGONE_PAYMENT_PATH . $path, $storeId);
        }
        return false;
    }

    /**
     * Return SHA1-in crypt key from config. Setup on admin place.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getShaInCode($storeId=null)
    {
        return $this->_encryptor->decrypt($this->getConfigData('secret_key_in', $storeId));
    }

    /**
     * Return SHA1-out crypt key from config. Setup on admin place.
     * @param int|null $storeId
     * @return string
     */
    public function getShaOutCode($storeId=null)
    {
        return $this->_encryptor->decrypt($this->getConfigData('secret_key_out', $storeId));
    }

    /**
     * Return gateway path, get from config. Setup on admin place.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getGatewayPath($storeId=null)
    {
        return $this->getConfigData('ogone_gateway', $storeId);
    }

    /**
     * Get PSPID, affiliation name in Ogone system
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPSPID($storeId=null)
    {
        return $this->getConfigData('pspid', $storeId);
    }

    /**
     * Get paypage template for magento style templates using
     *
     * @return string
     */
    public function getPayPageTemplate()
    {
        return $this->_urlBuilder->getUrl('ogone/api/paypage', array('_nosid' => true));
    }

    /**
     * Return url which Ogone system will use as accept
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/accept', array('_nosid' => true));
    }

    /**
     * Return url which Ogone system will use as decline url
     *
     * @return string
     */
    public function getDeclineUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/decline', array('_nosid' => true));
    }

    /**
     * Return url which ogone system will use as exception url
     *
     * @return string
     */
    public function getExceptionUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/exception', array('_nosid' => true));
    }

    /**
     * Return url which Ogone system will use as cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/cancel', array('_nosid' => true));
    }

    /**
     * Return url which Ogone system will use as our magento home url on Ogone success page
     *
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->_urlBuilder->getUrl('checkout/cart', array('_nosid' => true));
    }
}
