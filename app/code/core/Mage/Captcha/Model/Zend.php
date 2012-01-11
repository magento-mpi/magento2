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
 * Implementation of Zend_Captcha
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Zend extends Zend_Captcha_Image implements Mage_Captcha_Model_Interface
{
    const SESSION_CAPTCHA_ID = 'id';
    const SESSION_WORD = 'word';
    const DEFAULT_WORD_LENGTH_FROM = 3;
    const DEFAULT_WORD_LENGTH_TO   = 5;
    const SESSION_FAILED_ATTEMPTS = 'failed_attempts';

    /* @var Mage_Captcha_Helper_Interface */
    protected $_helper = null;
    // "alt" parameter of captcha's <img> tag
    protected $_imgAlt = "CAPTCHA";
    protected $_expiration;
    // Chance of garbage collection per captcha generation (1 = each time). Removes captcha image files for same formId
    // in case user clicked "refresh"
    protected $_gcFreq = 1;
    // Chance of parent garbage collection (which removes old files)
    protected $_parentGcFreq = 10;
    protected $_word;
    protected  $_formId;
    /**
     * @var Mage_Captcha_Model_Session
     */
    protected $_session;

    /**
     * Zend captcha constructor
     *
     * @param array $params
     */
    public function __construct($params)
    {
        if (!isset($params['formId'])) {
            throw new Exception('formId is mandatory');
        }
        $this->_formId = $params['formId'];
        $this->setExpiration($this->getTimeout());
    }

    /**
     * Get Block Name
     *
     * @return string
     */
    public function getBlockName()
    {
        return 'Mage_Captcha_Block_Captcha_Zend';
    }


    /**
     * Whether captcha is required to be inserted to this form
     *
     * @return bool
     */
    public function isRequired()
    {
        if ($this->_isUserAuth() || !$this->_isEnabled() || !in_array($this->_formId, $this->_getTargetForms())) {
            return false;
        }

        if ($this->_isShowAlways()) {
            return true;
        }

        $loggedFailedAttempts = (int)$this->getSession()->getDataIgnoreTtl(self::SESSION_FAILED_ATTEMPTS);
        $showAfterFailedAttempts = (int)$this->_getHelper()->getConfigNode('failed_attempts');
        return $loggedFailedAttempts >= $showAfterFailedAttempts;
    }

    /**
     * Check is user auth
     *
     * @return bool
     */
    protected function _isUserAuth()
    {
        return Mage::app()->getStore()->isAdmin()
            ? Mage::getSingleton('Mage_Admin_Model_Session')->isLoggedIn()
            : Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn();
    }

    /**
     * Whether to respect case while checking the answer
     *
     * @return bool
     */
    public function isCaseSensitive()
    {
        return (string)$this->_getHelper()->getConfigNode('case_sensitive');
    }

    /**
     * Get font to use when generating captcha
     *
     * @return string
     */
    public function getFont()
    {
        return $this->_getFontPath();
    }

    /**
     * After this time isCorrect() is going to return FALSE even if word was guessed correctly
     *
     * @return int
     */
    public function getTimeout()
    {
        if (!$this->_expiration) {
            /**
             * as "timeout" configuration parameter specifies timeout in minutes - we multiply it on 60 to set
             * expiration in seconds
             */
            $this->_expiration = (int)$this->_getHelper()->getConfigNode('timeout') * 60;
        }
        return $this->_expiration;
    }


    /**
     * Get captcha image directory
     *
     * @return string
     */
    public function getImgDir()
    {
        $captchaDir = Mage::getBaseDir('media') . DS . 'captcha' . DS;
        $io = new Varien_Io_File();
        $io->checkAndCreateFolder($captchaDir, 0755);
        return $captchaDir;
    }

    /**
     * Get captcha image base URL
     *
     * @return string
     */
    public function getImgUrl()
    {
        return Mage::getBaseUrl('media') . 'captcha/';
    }

    /**
     * Generate captcha
     *
     * @return string
     */
    public function generate()
    {
        $id = parent::generate();
        $this->getSession()->setData(self::SESSION_CAPTCHA_ID, $id);
        return $id;
    }

    /**
     * Checks whether captcha was guessed correctly by user
     *
     * @param string $word
     * @return bool
     */
    public function isCorrect($word)
    {
        $storedWord = $this->getSession()->getData(self::SESSION_WORD, true);

        if (!$word || !$storedWord){
            return false;
        }

        if (!$this->isCaseSensitive()) {
            $storedWord = strtolower($storedWord);
            $word = strtolower($word);
        }
        return $word == $storedWord;
    }

    /**
     * Returns session instance
     *
     * @return Mage_Captcha_Model_Session
     */
    public function getSession()
    {
        if (!$this->_session) {
            $params = array('formId' => $this->_formId, 'lifetime' => $this->getTimeout());
            $this->_session = Mage::getSingleton('Mage_Captcha_Model_Session', $params);
        }
        return $this->_session;
    }

     /**
     * Return full URL to captcha image
     *
     * @return string
     */
    public function getImgSrc()
    {
        return $this->getImgUrl() . $this->getId() . $this->getSuffix();
    }

    /**
     * log Attempt
     *
     * @return Captcha_Zend_Model_DB
     */
    public function logAttempt()
    {
        $attemptCount = (int)$this->getSession()->getDataIgnoreTtl(self::SESSION_FAILED_ATTEMPTS);
        $attemptCount++;
        $this->getSession()->setData(self::SESSION_FAILED_ATTEMPTS, $attemptCount);
        return $this;
    }

    /**
     * Returns path for the font file, chosen to generate captcha
     *
     * @return string
     */
    protected function _getFontPath()
    {
        $font = (string)$this->_getHelper()->getConfigNode('font');
        $fonts = $this->_getHelper()->getFonts();

        if (isset($fonts[$font])) {
            $fontPath = $fonts[$font]['path'];
        } else {
            $fontData = array_shift($fonts);
            $fontPath = $fontData['path'];
        }

        return $fontPath;
    }

    /**
     * Returns captcha helper
     *
     * @return Mage_Captcha_Helper_Interface
     */
    protected function _getHelper()
    {
        if (empty($this->_helper)) {
            $this->_helper = Mage::helper('Mage_Captcha_Helper_Data');
        }
        return $this->_helper;
    }

    /**
     * Generate word used for captcha render
     *
     * @return string
     */
    protected function _generateWord()
    {
        $word = '';
        $symbols = $this->_getSymbols();
        $worldLen = $this->_getWordLen();
        for ($i = 0; $i < $worldLen; $i++) {
            $word .= $symbols[array_rand($symbols)];
        }
        return $word;
    }

    /**
     * Get symbols array to use for word generation
     *
     * @return array
     */
    protected function _getSymbols()
    {
        return str_split((string)$this->_getHelper()->getConfigNode('symbols'));
    }

    /**
     * Returns length for generating captcha word. This value may be dynamic.
     *
     * @return int
     */
    protected function _getWordLen()
    {
        $from = 0;
        $to = 0;
        $length = (string)$this->_getHelper()->getConfigNode('length');
        if (!is_numeric($length)) {
            if (preg_match('/(\d+)-(\d+)/', $length, $matches)) {
                $from = (int)$matches[1];
                $to = (int)$matches[2];
            }
        } else {
            $from = (int)$length;
            $to = (int)$length;
        }

        if (($to < $from) || ($from < 1) || ($to < 1)) {
            $from = self::DEFAULT_WORD_LENGTH_FROM;
            $to = self::DEFAULT_WORD_LENGTH_TO;
        }

        return mt_rand($from, $to);
    }

    /**
     * Whether to show captcha for this form every time
     *
     * @return bool
     */
    protected function _isShowAlways()
    {
        if ((string)$this->_getHelper()->getConfigNode('mode') == Mage_Captcha_Helper_Data::MODE_ALWAYS){
            return true;
        }

        $alwaysFor = $this->_getHelper()->getConfigNode('always_for');
        foreach ($alwaysFor as $nodeFormId => $isAlwaysFor) {
            if ($isAlwaysFor && $this->_formId == $nodeFormId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether captcha is enabled at this area
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return (string)$this->_getHelper()->getConfigNode('enable');
    }

    /**
     * Retrieve list of forms where captcha must be shown
     *
     * For frontend this list is based on current website
     *
     * @return array
     */
    protected function _getTargetForms()
    {
        $formsString = (string) $this->_getHelper()->getConfigNode('forms');
        return explode(',', $formsString);
    }
}
