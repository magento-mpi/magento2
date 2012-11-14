<?php
/**
 * Unit test for Mage_Core_Model_Validator_Factory
 *
 * @copyright {}
 */
class Mage_Core_Model_Validator_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Translate_AdapterInterface|null
     */
    protected $_defaultTranslator = null;

    /**
     * Save default translator
     */
    protected function setUp()
    {
        $this->_defaultTranslator = Magento_Validator_ValidatorAbstract::getDefaultTranslator();
    }

    /**
     * Restore default translator
     */
    protected function tearDown()
    {
        Magento_Validator_ValidatorAbstract::setDefaultTranslator($this->_defaultTranslator);
        unset($this->_defaultTranslator);
    }

    /**
     * Test getValidatorConfig created correct validator config. Check that validator translator was initialized.
     */
    public function testGetValidatorConfig()
    {
        // Object manager mock
        $objectManagerMock = $this->getMockBuilder('Magento_ObjectManager_Zend')
            ->setMethods(array('create', 'get'))
            ->disableOriginalConstructor()
            ->getMock();

        $validatorConfigMock = $this->getMockBuilder('Magento_Validator_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento_Validator_Config', array('configFiles' => array('/tmp/moduleOne/etc/validation.xml')))
            ->will($this->returnValue($validatorConfigMock));

        $objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('Magento_Translate_Adapter')
            ->will($this->returnValue(new Magento_Translate_Adapter()));

        $objectManagerMock->expects($this->at(2))
            ->method('create')
            ->with('Mage_Core_Model_Translate_Expr')
            ->will($this->returnValue(new Mage_Core_Model_Translate_Expr()));

        // Config mock
        $configMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->setMethods(array('getModuleConfigurationFiles'))
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->with('validation.xml')
            ->will($this->returnValue(array('/tmp/moduleOne/etc/validation.xml')));

        // Translate adapter mock
        $translateAdapter = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->setConstructorArgs(array())
            ->setMethods(array('_getTranslatedString'))
            ->getMock();
        $translateAdapter->expects($this->once())
            ->method('_getTranslatedString')
            ->will($this->returnArgument(0));
        $factory = new Mage_Core_Model_Validator_Factory($objectManagerMock, $configMock, $translateAdapter);
        $actualConfig = $factory->getValidatorConfig();
        $this->assertInstanceOf('Magento_Validator_Config', $actualConfig,
            'Object of incorrect type was created');

        // Check that validator translator was correctly instantiated
        $validatorTranslator = Magento_Validator_ValidatorAbstract::getDefaultTranslator();
        $this->assertInstanceOf('Magento_Translate_Adapter', $validatorTranslator,
            'Default validator translate adapter was not set correctly');
        // Dive into callback
        /** @var Mage_Core_Model_Translate $translateAdapter */
        $this->assertEquals('Test message', $validatorTranslator->translate('Test message'),
            'Translator callback function was not initialized');
    }

    public function testCreateValidatorBuilder()
    {
        $this->markTestIncomplete('Test is incomplete');
        // Object manager mock
        $objectManagerMock = $this->getMockBuilder('Magento_ObjectManager_Zend')
            ->setMethods(array('create', 'get'))
            ->disableOriginalConstructor()
            ->getMock();

        $validatorConfigMock = $this->getMockBuilder('Magento_Validator_Config')
            ->setMethods(array('createValidatorBuilder', 'createValidator'))
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento_Validator_Config', array('configFiles' => array('/tmp/moduleOne/etc/validation.xml')))
            ->will($this->returnValue($validatorConfigMock));

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento_Translate_Adapter')
            ->will($this->returnValue(new Magento_Translate_Adapter()));

        // Config mock
        $configMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->setMethods(array('getModuleConfigurationFiles'))
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->with('validation.xml')
            ->will($this->returnValue(array('/tmp/moduleOne/etc/validation.xml')));

        // Translate adapter mock
        $translateAdapter = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->setConstructorArgs(array())
            ->setMethods(array('_getTranslatedString'))
            ->getMock();
        $translateAdapter->expects($this->once())
            ->method('_getTranslatedString')
            ->will($this->returnArgument(0));
        $factory = new Mage_Core_Model_Validator_Factory($objectManagerMock, $configMock, $translateAdapter);
        $actualConfig = $factory->getValidatorConfig();
        $this->assertInstanceOf('Magento_Validator_Config', $actualConfig,
            'Object of incorrect type was created');
    }
}
