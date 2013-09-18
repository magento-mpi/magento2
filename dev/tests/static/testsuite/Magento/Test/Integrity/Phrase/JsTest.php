<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

use \Magento\Tools\I18n\Code\Parser\Adapter;
use \Magento\Tools\I18n\Code\FilesCollector;

use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector;
use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer;

/**
 * Scan javascript files for invocations of mage.__() function, verifies that all the translations
 * were output to the page.
 */
class Magento_Test_Integrity_Phrase_JsTest extends Magento_Test_Integrity_Phrase_AbstractTestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Parser\Adapter\Js
     */
    protected $_parser;

    /** @var Magento_TestFramework_Utility_Files  */
    protected $_utilityFiles;

    /** @var Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector */
    protected $_phraseCollector;

    protected function setUp()
    {
        $this->_parser = new Adapter\Js();
        $this->_utilityFiles = Magento_TestFramework_Utility_Files::init();
        $this->_phraseCollector = new PhraseCollector(new Tokenizer());
    }

    public function testGetPhrasesAdminhtml()
    {
        $phrases = array();
        $unregisteredMessages = array();
        $untranslated = array();

        $registeredPhrases = $this->_getRegisteredPhrases('adminhtml');

        foreach ($this->_getJavascriptPhrases('adminhtml') as $phrase) {
            if (!in_array($phrase['phrase'], $registeredPhrases)) {
                $unregisteredMessages[]
                    = sprintf("'%s' \n in file %s, line# %s", $phrase['phrase'], $phrase['file'], $phrase['line']);
                $untranslated[] = $phrase['phrase'];
            }

        }

        if (count($unregisteredMessages) > 0) {
            $this->fail('There are UI messages in javascript files for adminhtml area ' .
                "which requires translations to be output to the page: \n\n"
                . implode("\n", $unregisteredMessages));
        }
    }

    public function testGetPhrasesFrontend()
    {
        $phrases = array();
        $unregisteredMessages = array();
        $untranslated = array();

        $registeredPhrases = $this->_getRegisteredPhrases('frontend');

        foreach ($this->_getJavascriptPhrases('frontend') as $phrase) {
            if (!in_array($phrase['phrase'], $registeredPhrases)) {
                $unregisteredMessages[]
                    = sprintf("'%s' \n in file %s, line# %s", $phrase['phrase'], $phrase['file'], $phrase['line']);
                $untranslated[] = $phrase['phrase'];
            }

        }

        if (count($unregisteredMessages) > 0) {
            $this->fail('There are UI messages in javascript files for frontend area ' .
                "which requires translations to be output to the page: \n\n"
                . implode("\n", $unregisteredMessages));
        }
    }

    protected function _getRegisteredPhrases($area)
    {

        $adminhtmlFile = __DIR__ .
            '../../../../../../../../../app/code/Magento/Adminhtml/view/adminhtml/page/head.phtml';
        $frontendFile =  __DIR__ .
            '../../../../../../../../../app/code/Magento/Page/view/frontend/html/head.phtml';

        switch ($area) {
            case 'adminhtml':
                $this->_phraseCollector->parse($adminhtmlFile);
                break;
            case 'frontend':
                $this->_phraseCollector->parse($frontendFile);
                break;
        }


        $result = array();
        foreach ($this->_phraseCollector->getPhrases() as $phrase) {
            $result[] = trim($phrase['phrase'], "'");
        }
        return $result;
    }

    protected function _getJavascriptPhrases($area)
    {
        $jsPhrases = array();
        foreach ($this->_utilityFiles->getJsFilesForArea($area) as $file) {
            $this->_parser->parse($file);
            $jsPhrases = array_merge($jsPhrases, $this->_parser->getPhrases());
        }
        return $jsPhrases;
    }
}