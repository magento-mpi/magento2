<?php
/**
 * Scan source code for detects invocations of outdated __() method
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Phrase_Legacy_SignatureTest extends Magento_Test_Integrity_Phrase_TestAbstract
{
    protected function setUp()
    {
        $this->_phraseCollector = new Magento_Test_Integrity_Phrase_Legacy_PhraseCollector();
    }

    public function testCase()
    {
        foreach ($this->_getFiles() as $file) {
            $this->_phraseCollector->parse($file);
            foreach ($this->_phraseCollector->getPhrases() as $phrase) {
                $this->addError($phrase);
            }
        }
        $this->assertEmpty(
            $this->_errors,
            $this->_prepareErrorMessage('%d usages of the old translation method call were discovered: %s',
                $this->_errors)
        );
    }
}
