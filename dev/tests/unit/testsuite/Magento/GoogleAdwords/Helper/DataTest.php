<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GoogleAdwords_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configNodeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var Magento_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_Config', array('getNode'), array(), '', false);
        $this->_storeConfigMock = $this->getMock(
            'Magento_Core_Model_Store_ConfigInterface', array(), array(), '', false
        );
        $this->_registryMock = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $this->_configNodeMock = $this->getMock('stdClass', array('asArray'), array(), '', false);

        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_helper = $objectManager->getObject('Magento_GoogleAdwords_Helper_Data', array(
            'config' => $this->_configMock,
            'storeConfig' => $this->_storeConfigMock,
            'registry' => $this->_registryMock,
        ));
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsActive()
    {
        return array(
            array(true, 1234, true),
            array(true, 'conversionId', false),
            array(true, '', false),
            array(false, '', false),
        );
    }

    /**
     * @param bool $isActive
     * @param string $returnConfigValue
     * @param bool $returnValue
     * @dataProvider dataProviderForTestIsActive
     */
    public function testIsGoogleAdwordsActive($isActive, $returnConfigValue, $returnValue)
    {
        $this->_storeConfigMock->expects($this->any())->method('getConfigFlag')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_ACTIVE)
            ->will($this->returnValue($isActive));
        $this->_storeConfigMock->expects($this->any())->method('getConfig')
            ->with($this->isType('string'))
            ->will($this->returnCallback(
                function () use ($returnConfigValue) {
                    return $returnConfigValue;
                }
            ));

        $this->assertEquals($returnValue, $this->_helper->isGoogleAdwordsActive());
    }

    public function testGetLanguageCodes()
    {
        $languages = array('en', 'ru', 'uk');
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_LANGUAGES)
            ->will($this->returnValue($this->_configNodeMock));
        $this->_configNodeMock->expects($this->once())->method('asArray')
            ->will($this->returnValue($languages));

        $this->assertEquals($languages, $this->_helper->getLanguageCodes());
    }

    public function dataProviderForTestConvertLanguage()
    {
        return array(
            array('some-language', 'some-language'),
            array('zh_TW', 'zh_Hant'),
            array('zh_CN', 'zh_Hans'),
            array('iw', 'he'),
        );
    }

    /**
     * @param string $language
     * @param string $returnLanguage
     * @dataProvider dataProviderForTestConvertLanguage
     */
    public function testConvertLanguageCodeToLocaleCode($language, $returnLanguage)
    {
        $convertArray = array('zh_TW' => 'zh_Hant', 'iw' => 'he', 'zh_CN' => 'zh_Hans');
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_LANGUAGE_CONVERT)
            ->will($this->returnValue($this->_configNodeMock));
        $this->_configNodeMock->expects($this->once())->method('asArray')
            ->will($this->returnValue($convertArray));

        $this->assertEquals($returnLanguage, $this->_helper->convertLanguageCodeToLocaleCode($language));
    }

    public function testGetConversionImgSrc()
    {
        $conversionId = 123;
        $label = 'LabEl';
        $imgSrc = sprintf('https://www.googleadservices.com/pagead/conversion/%s/?label=%s&amp;guid=ON&amp;script=0',
            $conversionId, $label);
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_IMG_SRC)->will($this->returnValue($imgSrc));
        $this->assertEquals($imgSrc, $this->_helper->getConversionImgSrc());
    }

    public function testGetConversionJsSrc()
    {
        $jsSrc = 'some-js-src';
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_JS_SRC)->will($this->returnValue($jsSrc));
        $this->assertEquals($jsSrc, $this->_helper->getConversionJsSrc());
    }

    /**
     * @return array
     */
    public function dataProviderForTestStoreConfig()
    {
        return array(
            array('getConversionId', Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_ID, 123),
            array('getConversionLanguage', Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_LANGUAGE, 'en'),
            array('getConversionFormat', Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_FORMAT, '2'),
            array('getConversionColor', Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_COLOR, 'ffffff'),
            array('getConversionLabel', Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_LABEL, 'Label'),
            array('getConversionValueType', Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE_TYPE, '1'),
            array('getConversionValueConstant', Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE, '0'),
        );
    }

    /**
     * @param string $method
     * @param string $xmlPath
     * @param string $returnValue
     * @dataProvider dataProviderForTestStoreConfig
     */
    public function testGetStoreConfigValue($method, $xmlPath, $returnValue)
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')->with($xmlPath)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->_helper->$method());
    }

    public function testGetConversionValueDynamic()
    {
        $returnValue = 4.1;
        $this->_storeConfigMock->expects($this->any())->method('getConfig')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE_TYPE)
            ->will($this->returnValue(Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_DYNAMIC));
        $this->_registryMock->expects($this->once())->method('registry')
            ->with(Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_REGISTRY_NAME)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->_helper->getConversionValue());
    }

    /**
     * @return array
     */
    public function dataProviderForTestConversionValueConstant()
    {
        return array(
            array(1.4, 1.4),
            array('', Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_DEFAULT),
        );
    }

    /**
     * @param string $conversionValueConst
     * @param string $returnValue
     * @dataProvider dataProviderForTestConversionValueConstant
     */
    public function testGetConversionValueConstant($conversionValueConst, $returnValue)
    {
        $this->_storeConfigMock->expects($this->at(0))->method('getConfig')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE_TYPE)
            ->will($this->returnValue(Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_CONSTANT));
        $this->_registryMock->expects($this->never())->method('registry');
        $this->_storeConfigMock->expects($this->at(1))->method('getConfig')
            ->with(Magento_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE)
            ->will($this->returnValue($conversionValueConst));

        $this->assertEquals($returnValue, $this->_helper->getConversionValue());
    }
}
