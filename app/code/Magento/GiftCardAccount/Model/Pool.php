<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model;

namespace Magento\GiftCardAccount\Model;

/**
 * @method \Magento\GiftCardAccount\Model\Resource\Pool _getResource()
 * @method \Magento\GiftCardAccount\Model\Resource\Pool getResource()
 * @method string getCode()
 * @method \Magento\GiftCardAccount\Model\Pool setCode(string $value)
 * @method int getStatus()
 * @method \Magento\GiftCardAccount\Model\Pool setStatus(int $value)
 */
class Pool extends \Magento\GiftCardAccount\Model\Pool\AbstractPool
{
    const CODE_FORMAT_ALPHANUM = 'alphanum';
    const CODE_FORMAT_ALPHA = 'alpha';
    const CODE_FORMAT_NUM = 'num';

    const XML_CONFIG_CODE_FORMAT = 'giftcard/giftcardaccount_general/code_format';
    const XML_CONFIG_CODE_LENGTH = 'giftcard/giftcardaccount_general/code_length';
    const XML_CONFIG_CODE_PREFIX = 'giftcard/giftcardaccount_general/code_prefix';
    const XML_CONFIG_CODE_SUFFIX = 'giftcard/giftcardaccount_general/code_suffix';
    const XML_CONFIG_CODE_SPLIT  = 'giftcard/giftcardaccount_general/code_split';
    const XML_CONFIG_POOL_SIZE   = 'giftcard/giftcardaccount_general/pool_size';
    const XML_CONFIG_POOL_THRESHOLD = 'giftcard/giftcardaccount_general/pool_threshold';

    const CODE_GENERATION_ATTEMPTS = 1000;

    /**
     * @var array
     */
    protected $_giftCardCodeParams = array();

    /**
     * Store Manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $giftCardCodeParams
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $giftCardCodeParams = array(),
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_storeManager = $storeManager;
        $this->_giftCardCodeParams = $giftCardCodeParams;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftCardAccount\Model\Resource\Pool');
    }

    /**
     * Generate Pool
     *
     * @return $this
     * @throws \Magento\Core\Exception
     */
    public function generatePool()
    {
        $this->cleanupFree();

        $website = $this->_storeManager->getWebsite($this->getWebsiteId());
        $size = $website->getConfig(self::XML_CONFIG_POOL_SIZE);

        for ($i = 0; $i < $size; $i++) {
            $attempt = 0;
            do {
                if ($attempt>=self::CODE_GENERATION_ATTEMPTS) {
                    throw new \Magento\Core\Exception(
                        __('We were unable to create full code pool size. Please check settings and try again.')
                    );
                }
                $code = $this->_generateCode();
                $attempt++;
            } while ($this->getResource()->exists($code));

            $this->getResource()->saveCode($code);
        }
        return $this;
    }

    /**
     * Checks pool threshold and call codes generation in case if free codes count is less than threshold value
     *
     * @return $this
     */
    public function applyCodesGeneration()
    {
        $website = $this->_storeManager->getWebsite($this->getWebsiteId());
        $threshold = $website->getConfig(self::XML_CONFIG_POOL_THRESHOLD);
        if ($this->getPoolUsageInfo()->getFree() < $threshold) {
            $this->generatePool();
        }
        return $this;
    }

    /**
     * Generate gift card code
     *
     * @return string
     */
    protected function _generateCode()
    {
        $website = $this->_storeManager->getWebsite($this->getWebsiteId());

        $format  = $website->getConfig(self::XML_CONFIG_CODE_FORMAT);
        if (!$format) {
            $format = 'alphanum';
        }
        $length  = max(1, (int) $website->getConfig(self::XML_CONFIG_CODE_LENGTH));
        $split   = max(0, (int) $website->getConfig(self::XML_CONFIG_CODE_SPLIT));
        $suffix  = $website->getConfig(self::XML_CONFIG_CODE_SUFFIX);
        $prefix  = $website->getConfig(self::XML_CONFIG_CODE_PREFIX);

        $splitChar = $this->getCodeSeparator();
        $charset = isset($this->_giftCardCodeParams['charset'][$format])
            ? $this->_giftCardCodeParams['charset'][$format]
            : '';
        $charset = str_split($charset);

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $char = $charset[array_rand($charset)];
            if ($split > 0 && ($i%$split) == 0 && $i != 0) {
                $char = "{$splitChar}{$char}";
            }
            $code .= $char;
        }

        $code = "{$prefix}{$code}{$suffix}";
        return $code;
    }

    /**
     * @return string
     */
    public function getCodeSeparator()
    {
        return isset($this->_giftCardCodeParams['separator']) ? $this->_giftCardCodeParams['separator'] : '';
    }
}
