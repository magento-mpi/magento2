<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Captcha\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Captcha image model
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Used for "name" attribute of captcha's input field
     */
    const INPUT_NAME_FIELD_VALUE = 'captcha';

    /**
     * Always show captcha
     */
    const MODE_ALWAYS = 'always';

    /**
     * Show captcha only after certain number of unsuccessful attempts
     */
    const MODE_AFTER_FAIL = 'after_fail';

    /**
     * Captcha fonts path
     */
    const XML_PATH_CAPTCHA_FONTS = 'captcha/fonts';

    /**
     * Default captcha type
     */
    const DEFAULT_CAPTCHA_TYPE = 'Zend';

    /**
     * List uses Models of Captcha
     * @var array
     */
    protected $_captcha = array();

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Captcha\Model\CaptchaFactory
     */
    protected $_factory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Captcha\Model\CaptchaFactory $factory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Captcha\Model\CaptchaFactory $factory
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_filesystem = $filesystem;
        $this->_factory = $factory;
        parent::__construct($context);
    }

    /**
     * Get Captcha
     *
     * @param string $formId
     * @return \Magento\Captcha\Model\ModelInterface
     */
    public function getCaptcha($formId)
    {
        if (!array_key_exists($formId, $this->_captcha)) {
            $captchaType = ucfirst($this->getConfig('type'));
            if (!$captchaType) {
                $captchaType = self::DEFAULT_CAPTCHA_TYPE;
            } elseif ($captchaType == 'Default') {
                $captchaType = $captchaType . 'Model';
            }

            $this->_captcha[$formId] = $this->_factory->create($captchaType, $formId);
        }
        return $this->_captcha[$formId];
    }

    /**
     * Returns config value
     *
     * @param string $key The last part of XML_PATH_$area_CAPTCHA_ constant (case insensitive)
     * @param \Magento\Store\Model\Store $store
     * @return \Magento\Framework\App\Config\Element
     */
    public function getConfig($key, $store = null)
    {
        return $this->_config->getValue(
            'customer/captcha/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get list of available fonts.
     *
     * Return format:
     * [['arial'] => ['label' => 'Arial', 'path' => '/www/magento/fonts/arial.ttf']]
     *
     * @return array
     */
    public function getFonts()
    {
        $fontsConfig = $this->_config->getValue(\Magento\Captcha\Helper\Data::XML_PATH_CAPTCHA_FONTS, 'default');
        $fonts = array();
        if ($fontsConfig) {
            $libDir = $this->_filesystem->getPath(DirectoryList::LIB_INTERNAL);
            foreach ($fontsConfig as $fontName => $fontConfig) {
                $fonts[$fontName] = array(
                    'label' => $fontConfig['label'],
                    'path' => $libDir . '/' . $fontConfig['path']
                );
            }
        }
        return $fonts;
    }

    /**
     * Get captcha image directory
     *
     * @param mixed $website
     * @return string
     */
    public function getImgDir($website = null)
    {
        $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA_DIR);
        $captchaDir = '/captcha/' . $this->_getWebsiteCode($website);
        $mediaDir->create($captchaDir);
        $mediaDir->changePermissions($captchaDir, 0775);

        return $mediaDir->getAbsolutePath($captchaDir) . '/';
    }

    /**
     * Get website code
     *
     * @param mixed $website
     * @return string
     */
    protected function _getWebsiteCode($website = null)
    {
        return $this->_storeManager->getWebsite($website)->getCode();
    }

    /**
     * Get captcha image base URL
     *
     * @param mixed $website
     * @return string
     */
    public function getImgUrl($website = null)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            DirectoryList::MEDIA_DIR
        ) . 'captcha' . '/' . $this->_getWebsiteCode(
            $website
        ) . '/';
    }
}
