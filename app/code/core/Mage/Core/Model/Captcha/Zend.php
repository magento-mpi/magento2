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
    const SESSION_CAPTCHA_ID = 'id';
    const SESSION_WORD = 'word';
    const DEFAULT_WORD_LENGTH_FROM = 3;
    const DEFAULT_WORD_LENGTH_TO   = 5;

    /* @var Mage_Core_Model_Captcha_Session */
    protected $_session = null;
    /* @var Mage_Core_Helper_Captcha */
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

    /**
     * Zend captcha constructor
     *
     * @param string $formId
     */
    public function __construct($formId)
    {
        if (empty($formId)) {
            Mage::throwException('$formId can not be empty');
        }
        $this->_formId = $formId;
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
    public function isCaseSensitive()
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
        if (!$this->_expiration) {
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
     * Garbage collector. Removes old captcha image file in case user clicked "refresh".
     *
     * @return Mage_Core_Model_Captcha_Zend
     */
    protected function _gc()
    {
        $oldId = $this->getSession()->getData(self::SESSION_CAPTCHA_ID);
        if ($oldId) {
            // An image for same form already exists - it won't be used after new captcha is generated, we can remove it
            $filename = $this->getImgDir() . $oldId . $this->getSuffix();
            if (file_exists($filename)) {
                @unlink($filename);
            }
        }
        if (mt_rand(1, $this->_parentGcFreq) == 1) {
            parent::_gc();
        }
        return $this;
    }

    /**
     * Checks whether captcha was guessed correctly by user
     *
     * @param string $word
     * @return bool
     */
    public function isCorrect($word)
    {
        if (!$this->getSession()->getDataIgnoreTtl(self::SESSION_CAPTCHA_ID, true)) {
            // Captcha has not been generated
            return true;
        }
        $storedWord = $this->getSession()->getData(self::SESSION_WORD, true);
        if (!$this->isCaseSensitive()) {
            $storedWord = strtolower($storedWord);
            $word = strtolower($word);
        }
        return ($word == $storedWord);
    }

    /**
     * Returns session instance
     *
     * @return Mage_Core_Model_Captcha_Session
     */
    public function getSession()
    {
        if (!$this->_session) {
            $this->_session = $this->_getHelper()->getSession($this->_formId);
            $this->_session->setLifetime($this->getTimeout());
        }
        return $this->_session;
    }
}
