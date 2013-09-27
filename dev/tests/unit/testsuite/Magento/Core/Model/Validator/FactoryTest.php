<?php
/**
 * Unit test for Magento_Core_Model_Validator_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Validator_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translateAdapter;

    /**
     * @var Magento_Validator_Config
     */
    protected $_validatorConfig;

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
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_validatorConfig = $this->getMockBuilder('Magento_Validator_Config')
            ->setMethods(array('createValidatorBuilder', 'createValidator'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with('Magento_Translate_Adapter')
            ->will($this->returnValue(new Magento_Translate_Adapter()));

        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with('Magento_Validator_Config', array('configFiles' => array('/tmp/moduleOne/etc/validation.xml')))
            ->will($this->returnValue($this->_validatorConfig));

        // Config mock
        $this->_config = $this->getMockBuilder('Magento_Core_Model_Config_Modules_Reader')
            ->setMethods(array('getConfigurationFiles'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_config->expects($this->once())
            ->method('getConfigurationFiles')
            ->with('validation.xml')
            ->will($this->returnValue(array('/tmp/moduleOne/etc/validation.xml')));

        // Translate adapter mock
        $this->_translateAdapter = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->setMethods(array('_getTranslatedString', 'translate'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_translateAdapter->expects($this->any())
            ->method('_getTranslatedString')
            ->will($this->returnArgument(0));
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
        $factory = new Magento_Core_Model_Validator_Factory(
            $this->_objectManager,
            $this->_config,
            $this->_translateAdapter
        );
        $actualConfig = $factory->getValidatorConfig();
        $this->assertInstanceOf('Magento_Validator_Config', $actualConfig,
            'Object of incorrect type was created');

        // Check that validator translator was correctly instantiated
        $validatorTranslator = Magento_Validator_ValidatorAbstract::getDefaultTranslator();
        $this->assertInstanceOf('Magento_Translate_Adapter', $validatorTranslator,
            'Default validator translate adapter was not set correctly');
    }

    /**
     * Test createValidatorBuilder call
     */
    public function testCreateValidatorBuilder()
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_validatorConfig->expects($this->once())
            ->method('createValidatorBuilder')
            ->with('test', 'class', array())
            ->will($this->returnValue(
                $objectManager->getObject('Magento_Validator_Builder', array('constraints' => []))
            ));
        $factory = new Magento_Core_Model_Validator_Factory(
            $this->_objectManager,
            $this->_config,
            $this->_translateAdapter
        );
        $this->assertInstanceOf('Magento_Validator_Builder',
            $factory->createValidatorBuilder('test', 'class', array()));
    }

    /**
     * Test createValidatorBuilder call
     */
    public function testCreateValidator()
    {
        $this->_validatorConfig->expects($this->once())
            ->method('createValidator')
            ->with('test', 'class', array())
            ->will($this->returnValue(new Magento_Validator()));
        $factory = new Magento_Core_Model_Validator_Factory($this->_objectManager, $this->_config,
            $this->_translateAdapter);
        $this->assertInstanceOf('Magento_Validator',
            $factory->createValidator('test', 'class', array()));
    }
}
