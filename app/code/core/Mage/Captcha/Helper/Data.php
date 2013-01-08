<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Helper_Data extends Mage_Core_Helper_Abstract
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
    const XML_PATH_CAPTCHA_FONTS = 'default/captcha/fonts';

    /**
     * List uses Models of Captcha
     * @var array
     */
    protected $_captcha = array();

    /**
     * @var Mage_Core_Model_Config_Options
     */
    protected $_option;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Config $config
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Mage_Core_Model_App $app,
        Mage_Core_Model_Config $config,
        Magento_Filesystem $filesystem
    ) {
        $this->_app = $app;
        $this->_config = $config;
        $this->_filesystem = $filesystem;
    }

    /**
     * Get Captcha
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Interface
     */
    public function getCaptcha($formId)
    {
        if (!array_key_exists($formId, $this->_captcha)) {
            $type = ucfirst($this->getConfigNode('type'));
            $this->_captcha[$formId] = $this->_config->getModelInstance(
                'Mage_Captcha_Model_' . $type,
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
     * @param string $id The last part of XML_PATH_$area_CAPTCHA_ constant (case insensitive)
     * @param Mage_Core_Model_Store $store
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfigNode($id, $store = null)
    {
        $store = $this->_app->getStore($store);
        $areaCode = $store->isAdmin() ? 'admin' : 'customer';
        return $store->getConfig($areaCode . '/captcha/' . $id, $store);
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
        $node = $this->_config->getNode(Mage_Captcha_Helper_Data::XML_PATH_CAPTCHA_FONTS);
        $fonts = array();
        if ($node) {
            foreach ($node->children() as $fontName => $fontNode) {
                $fonts[$fontName] = array(
                    'label' => (string)$fontNode->label,
                    'path' => $this->_config->getOptions()->getDir('base') . DIRECTORY_SEPARATOR . $fontNode->path
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
        $captchaDir = Magento_Filesystem::getPathFromArray(array($this->_config->getOptions()->getDir('media'),
            'captcha', $this->_app->getWebsite($website)->getCode()));
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($captchaDir, 0755);
        return $captchaDir . Magento_Filesystem::DIRECTORY_SEPARATOR;
    }

    /**
     * Get captcha image base URL
     *
     * @param mixed $website
     * @return string
     */
    public function getImgUrl($website = null)
    {
        return $this->_app->getStore()->getBaseUrl('media') . 'captcha'
            . '/' . $this->_app->getWebsite($website)->getCode() . '/';
    }
}
