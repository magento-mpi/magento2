<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Implementation of Zend_Captcha
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Captcha_Zend extends Zend_Captcha_Image implements Mage_Core_Model_Captcha_Interface
{
    const COOKIE_NAME = 'captcha';
    const SESSION_TOKEN_NAME = 'captcha_token';
    const SESSION_TIMEOUT_NAME = 'timeout';
    const DEFAULT_WORD_LENGTH_FROM = 3;
    const DEFAULT_WORD_LENGTH_TO   = 5;

    /* @var Mage_Core_Model_Session */
    protected $_session = null;
    /* @var Mage_Core_Helper_Captcha */
    protected $_helper = null;
    // "alt" parameter of captcha's <img> tag
    protected $_imgAlt = "CAPTCHA";

    /**
     * Saves timestamp when captcha was generated
     *
     * @return Mage_Core_Model_Captcha_Zend
     */
    protected function _setTimestamp()
    {
        $this->getSession()->setData(self::SESSION_TIMEOUT_NAME, time());
        return $this;
    }

    /**
     * Retrieves timestamp when captcha was generated
     *
     * @return int
     */
    protected function _getTimestamp()
    {
        return (int)$this->getSession()->getData(self::SESSION_TIMEOUT_NAME);
    }

    /**
     * Saves captcha token (salt)
     *
     * @param string $token
     * @return Mage_Core_Model_Captcha_Zend
     */
    protected function _setToken($token)
    {
        $this->getSession()->setData(self::SESSION_TOKEN_NAME, $token);
        return $this;
    }

    /**
     * Retrieves captcha token (salt)
     *
     * @return string
     */
    protected function _getToken()
    {
        return $this->getSession()->getData(self::SESSION_TOKEN_NAME);
    }

    /**
     * Removes stored token (salt)
     *
     * @return Mage_Core_Model_Captcha_Zend
     */
    protected function _removeToken()
    {
        $this->getSession()->unsetData(self::SESSION_TOKEN_NAME);
        return $this;
    }

    /**
     * Returns hash of the generated word with salt
     *
     * @param string $word
     * @param string $token
     * @return string
     */
    protected function _getHash($word, $token)
    {
        if (!$this->_isCaseSensitive()) {
            $word = strtolower($word);
        }
        $hash = md5($word . $token);
        return $hash;
    }

    /**
     * Generates captcha token (salt)
     *
     * @return string
     */
    protected function _generateToken()
    {
        $token = '';
        for ($i = 0, $to = 8; $i < $to; $i++) {
            $token .= chr(mt_rand(48, 122));
        }
        return $token;
    }

    /**
     * Whether captcha was generated on previous page. In other words, do we expect captcha guess in the form data.
     *
     * @return bool
     */
    protected function _isGenerated()
    {
        $token = $this->_getToken();
        return !empty($token);
    }

    /**
     * Returns path for the font file, chosen to generate captcha
     *
     * @return string
     */
    protected function _getFontPath()
    {
        $helper = $this->_getHelper();
        $font = (string)$helper->getConfigNode('font');
        $fonts = $helper->getFonts();
        $fontPath = '';
        if (!isset($fonts[$font])) {
            // Font specified in <font> section is not defined in <fonts> section, using first font from defined list
            foreach ($fonts as $fontData) {
                $fontPath = $fontData['path'];
                break;
            }
        } else {
            $fontPath = $fonts[$font]['path'];
        }
        return $fontPath;
    }

    /**
     * Returns captcha helper
     *
     * @return Mage_Core_Helper_Captcha
     */
    protected function _getHelper()
    {
        if (empty($this->_helper)) {
            $this->_helper = Mage::helper('core/captcha');
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
        $wordLen = $this->getWordLen();
        $symbols = $this->_getSymbols();
        for ($i = 0; $i < $wordLen; $i++) {
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
        $symbolsStr = (string)$this->_getHelper()->getConfigNode('symbols');
        $symbols = str_split($symbolsStr);
        return $symbols;
    }

    /**
     * Whether to respect case while checking the answer
     *
     * @return bool
     */
    protected  function _isCaseSensitive()
    {
        $isCaseSensitive = (bool)(string)$this->_getHelper()->getConfigNode('case_sensitive');
        return $isCaseSensitive;
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
        $timeout = (int)$this->_getHelper()->getConfigNode('timeout');
        return $timeout;
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
     * Returns length for generating captcha word. This value may be dynamic.
     *
     * @return int
     */
    public function getWordLen()
    {
        $from = 0;
        $to = 0;
        $length = (string)$this->_getHelper()->getConfigNode('word_length');
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

        $lengthForThisWord = mt_rand($from, $to);
        return $lengthForThisWord;
    }

    /**
     * Returns session object used to store captcha data between page refreshes
     *
     * @return Mage_Core_Model_Session
     */
    public function getSession()
    {
        if (!$this->_session) {
            $this->_session = Mage::getSingleton('core/session');
        }
        return $this->_session;
    }

    /**
     * Generate captcha
     *
     * @return string
     */
    public function generate()
    {
        $id = parent::generate();
        $token = $this->_generateToken();
        $this->_setToken($token)->_setTimestamp();
        $hash = $this->_getHash($this->getWord(), $token);
        /* @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('core/cookie');
        $cookie->set(self::COOKIE_NAME, $hash, $this->getTimeout());
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
        $isGenerated = $this->_isGenerated();
        if (!$isGenerated) {
            return true;
        }
        $storedTimestamp = $this->_getTimestamp();
        $isCorrect = false;
        if ($isGenerated && $storedTimestamp) {
            $isCorrect = (time() - $storedTimestamp) <= $this->getTimeout();
            if ($isCorrect) {
                /* @var $cookie Mage_Core_Model_Cookie */
                $cookie = Mage::getSingleton('core/cookie');
                $computedHash = $this->_getHash($word, $this->_getToken());
                // Token is a one-time thing, we do not need it anymore
                $this->_removeToken();
                $storedHash = $cookie->get(self::COOKIE_NAME);
                $isCorrect = ($computedHash == $storedHash);
            }
        }
        return $isCorrect;
    }
}
