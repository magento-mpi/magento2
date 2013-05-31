<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_GoogleAdwords_Model_Config_Source_LanguageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localeMock;

    /**
     * @var Zend_Locale
     */
    protected $_currentLocale;

    /**
     * @var Mage_GoogleAdwords_Model_Filter_UppercaseTitle
     */
    protected $_uppercaseFilter;

    /**
     * @var Mage_GoogleAdwords_Model_Config_Source_Language
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleAdwords_Helper_Data', array(), array(), '', false);
        $this->_currentLocale =  new Zend_Locale();
        $this->_localeMock = $this->getMock('Mage_Core_Model_LocaleInterface', array(), array(), '', false);
        $this->_localeMock->expects($this->atLeastOnce())->method('getLocale')
            ->will($this->returnValue($this->_currentLocale));
        $this->_uppercaseFilter = new Mage_GoogleAdwords_Model_Filter_UppercaseTitle();

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Mage_GoogleAdwords_Model_Config_Source_Language', array(
            'locale' => $this->_localeMock,
            'helper' => $this->_helperMock,
            'uppercaseFilter' => $this->_uppercaseFilter,
        ));
    }

    /**
     * Get language label
     *
     * @param string $language
     * @return string
     */
    protected function _getLanguageLabel($language)
    {
        $this->_helperMock->expects($this->any())->method('convertLanguageToCurrentLocale')
            ->with($this->isType('string'))->will($this->returnCallback(function ($returnLanguage) use ($language) {
                return $returnLanguage;
            }));
        $languageLocaleName = $this->_uppercaseFilter->filter(
            $this->_currentLocale->getTranslation($language, 'language', $language)
        );
        $languageName = $this->_currentLocale->getTranslation($language, 'language');
        return sprintf('%s / %s (%s)', $languageLocaleName, $languageName, $language);
    }

    public function testToOptionArray()
    {
        $languages = array('en', 'ru');
        $languagesReturn = array(
            array(
                'value' => 'en',
                'label' => $this->_getLanguageLabel('en'),
            ),
            array(
                'value' => 'ru',
                'label' => $this->_getLanguageLabel('ru'),
            ),
        );
        $this->_helperMock->expects($this->once())->method('getLanguageCodes')
            ->will($this->returnValue($languages));
        $this->assertEquals($languagesReturn, $this->_model->toOptionArray());
    }
}
