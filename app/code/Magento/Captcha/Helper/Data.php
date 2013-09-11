<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Magento
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Used for "name" attribute of captcha's input field
     */
    const INPUT_NAME_FIELD_VALUE = 'captcha';

    /**
     * Always show captcha
     */
    const MODE_ALWAYS     = 'always';

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
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs = null;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Captcha\Model\CaptchaFactory
     */
    protected $_factory;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Captcha\Model\CaptchaFactory $factory
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Dir $dirs,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Filesystem $filesystem,
        \Magento\Captcha\Model\CaptchaFactory $factory
    ) {
        $this->_dirs = $dirs;
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
            $type = ucfirst($this->getConfigNode('type'));
            if (!$type) {
                $type = self::DEFAULT_CAPTCHA_TYPE;
            }
            $this->_captcha[$formId] = $this->_factory->create(
                'Magento_Captcha_Model_' . $type,
                array(
                    'params' => array('formId' => $formId, 'helper' => $this)
                )
            );
        }
        return $this->_captcha[$formId];
    }

    /**
     * Returns value of the node with respect to current area (frontend or backend)
     *
     * @param string $key The last part of XML_PATH_$area_CAPTCHA_ constant (case insensitive)
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\Core\Model\Config\Element
     */
    public function getConfigNode($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $areaCode = $store->isAdmin() ? 'admin' : 'customer';
        return $store->getConfig($areaCode . '/captcha/' . $key);
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
            $libDir = $this->_dirs->getDir(\Magento\Core\Model\Dir::LIB);
            foreach ($fontsConfig as $fontName => $fontConfig) {
                $fonts[$fontName] = array(
                    'label' => $fontConfig['label'],
                    'path' => $libDir . DIRECTORY_SEPARATOR . $fontConfig['path']
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
        $mediaDir =  $this->_dirs->getDir(\Magento\Core\Model\Dir::MEDIA);
        $captchaDir = $mediaDir . '/captcha/' . $this->_storeManager->getWebsite($website)->getCode();
        $this->_filesystem->setWorkingDirectory($mediaDir);
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($captchaDir, 0775);
        return $captchaDir . '/';
    }

    /**
     * Get captcha image base URL
     *
     * @param mixed $website
     * @return string
     */
    public function getImgUrl($website = null)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Core\Model\Dir::MEDIA) . 'captcha'
            . '/' . $this->_storeManager->getWebsite($website)->getCode() . '/';
    }
}
