<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for abstract export model
 */
class EntityAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Export\EntityAbstract
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        /** @var Magento_TestFramework_ObjectManager  $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $storeConfig = $objectManager->get('Magento\Core\Model\Store\Config');
        $this->_model = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Export\EntityAbstract', array($storeConfig)
        );
    }

    /**
     * Check methods which provide ability to manage errors
     */
    public function testAddRowError()
    {
        $errorCode = 'test_error';
        $errorNum = 1;
        $errorMessage = 'Test error!';
        $this->_model->addMessageTemplate($errorCode, $errorMessage);
        $this->_model->addRowError($errorCode, $errorNum);

        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertEquals(1, $this->_model->getInvalidRowsCount());
        $this->assertArrayHasKey($errorMessage, $this->_model->getErrorMessages());
    }

    /**
     * Check methods which provide ability to manage writer object
     */
    public function testGetWriter()
    {
        $this->_model->setWriter(\Mage::getModel('Magento\ImportExport\Model\Export\Adapter\Csv'));
        $this->assertInstanceOf('Magento\ImportExport\Model\Export\Adapter\Csv', $this->_model->getWriter());
    }

    /**
     * Check that method throw exception when writer was not defined
     *
     * @expectedException \Magento\Core\Exception
     */
    public function testGetWriterThrowsException()
    {
        $this->_model->getWriter();
    }

    /**
     * Test for method filterAttributeCollection
     */
    public function testFilterAttributeCollection()
    {
        /** @var $model \Magento\ImportExport\Model\Export\StubEntityAbstract */
        $model = $this->getMockForAbstractClass('\Magento\ImportExport\Model\Export\StubEntityAbstract');
        $collection = \Mage::getResourceModel('Magento\Customer\Model\Resource\Attribute\Collection');
        $collection = $model->filterAttributeCollection($collection);
        /**
         * Check that disabled attributes is not existed in attribute collection
         */
        $existedAttributes = array();
        /** @var $attribute \Magento\Customer\Model\Attribute */
        foreach ($collection as $attribute) {
            $existedAttributes[] = $attribute->getAttributeCode();
        }
        $disabledAttributes = $model->getDisabledAttributes();
        foreach ($disabledAttributes as $attributeCode) {
            $this->assertNotContains(
                $attributeCode,
                $existedAttributes,
                'Disabled attribute "' . $attributeCode . '" existed in collection'
            );
        }
    }
}

/**
 * Stub abstract class which provide to change protected property "$_disabledAttrs" and test methods depended on it
 */
abstract class Stub_Magento_ImportExport_Model_Export_EntityAbstract
    extends \Magento\ImportExport\Model\Export\EntityAbstract
{
    public function __construct()
    {
        /** @var Magento_TestFramework_ObjectManager  $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $storeConfig = $objectManager->get('Magento\Core\Model\Store\Config');
        parent::__construct($storeConfig);
        $this->_disabledAttrs = array('default_billing', 'default_shipping');
    }
}
