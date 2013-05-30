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
    protected $_zendLocale;

    /**
     * @var Mage_GoogleAdwords_Model_Config_Source_Language
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleAdwords_Helper_Data', array(), array(), '', false);
        $this->_zendLocale =  new Zend_Locale();
        $this->_localeMock = $this->getMock('Mage_Core_Model_LocaleInterface', array(), array(), '', false);
        $this->_localeMock->expects($this->atLeastOnce())->method('getLocale')
            ->will($this->returnValue($this->_zendLocale));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Mage_GoogleAdwords_Model_Config_Source_Language', array(
            'locale' => $this->_localeMock,
            'helper' => $this->_helperMock,
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
        $languageLocaleName = $this->_zendLocale->getTranslation($language, 'language', $language);
        $languageName = $this->_zendLocale->getTranslation($language, 'language');
        if (function_exists('mb_convert_case')) {
            $languageLocaleName = mb_convert_case($languageLocaleName, MB_CASE_TITLE, 'UTF-8');
        } else {
            $languageLocaleName = ucwords($languageLocaleName);
        }
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
