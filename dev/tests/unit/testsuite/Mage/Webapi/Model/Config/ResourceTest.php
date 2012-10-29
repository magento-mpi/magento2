<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Config_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Config_Resource
     */
    protected $_model = null;

    protected function setUp()
    {
        $configData = include __DIR__ . '/_files/resource_config_data.php';
        $this->_model = new Mage_Webapi_Model_Config_Resource(array(
            'directoryScanner' => new \Zend\Code\Scanner\DirectoryScanner(),
            'applicationConfig' => new Mage_Core_Model_Config(),
            'data' => $configData,
            'helper' => new Mage_Webapi_Helper_Data()
        ));
    }

    protected function tearDown()
    {
        unset($this->_model);
        parent::tearDown();
    }

    /**
     * @dataProvider dataProviderTestGetResourceNameByOperationPositive
     * @param string $operation
     * @param string $resourceVersion
     * @param string $expectedResourceName
     * @param string $message
     */
    public function testGetResourceNameByOperationPositive($operation, $resourceVersion, $expectedResourceName,
        $message = 'Resource name was identified incorrectly by given operation.'
    ) {
        $actualResourceName = $this->_model->getResourceNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetResourceNameByOperationPositive()
    {
        return array(
            array('customerUpdate', 'v1', 'customer'),
            array('customerUpdate', '1', 'customer',
                "Resource was identified incorrectly by version without 'v' prefix"),
            array('customerMultiUpdate', 'v1', 'customer', 'Compound method names seem be be identified incorrectly.'),
            array('enterpriseCatalogProductGet', 'v1', 'enterpriseCatalogProduct',
                'Compound resource name is identified incorrectly.'),
            array('customerMultiDelete', 'v2', 'customer', 'Version seems to be processed incorrectly.'),
            array('customerMultiDelete', null, 'customer',
                "If version is not set - no check must be performed for operation existence in resource."),
        );
    }

    /**
     * @dataProvider dataProviderTestGetResourceNameByOperationNegative
     * @param string $operation
     * @param string $resourceVersion
     * @param string $expectedResourceName
     * @param string $message
     */
    public function testGetResourceNameByOperationNegative($operation, $resourceVersion, $expectedResourceName,
        $message = 'Resource name was identified incorrectly by given operation.'
    ) {
        $actualResourceName = $this->_model->getResourceNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetResourceNameByOperationNegative()
    {
        return array(
            array('customerMultiDeleteExcessiveSuffix', 'v2', false, 'Excessive suffix is ignored.'),
            array('customerInvalid', 'v1', false, "In case when operation not found 'false' is expected."),
            array('customerUpdate', 'v100', false, "In case when version not found 'false' is expected."),
        );
    }

    /**
     * @dataProvider dataProviderTestGetMethodNameByOperation
     * @param string $operation
     * @param string $resourceVersion
     * @param string $expectedResourceName
     * @param string $message
     */
    public function testGetMethodNameByOperation($operation, $resourceVersion, $expectedResourceName,
        $message = 'Resource name was identified incorrectly by given operation.'
    ) {
        $actualResourceName = $this->_model->getMethodNameByOperation($operation, $resourceVersion);
        $this->assertEquals($expectedResourceName, $actualResourceName, $message);
    }

    public function dataProviderTestGetMethodNameByOperation()
    {
        return array(
            array('customerUpdate', 'v1', 'update'),
            array('customerMultiUpdate', 'v1', 'multiUpdate',
                'Compound method names seem be be identified incorrectly.'),
            array('enterpriseCatalogProductGet', 'v1', 'get',
                'Compound resource name is identified incorrectly.'),
            array('customerMultiDelete', 'v2', 'multiDelete', 'Version seems to be processed incorrectly.'),
            array('customerMultiDeleteExcessiveSuffix', 'v2', false, 'Excessive suffix is ignored.'),
            array('customerInvalid', 'v1', false, "In case when operation not found 'false' is expected."),
            array('customerUpdate', 'v100', false, "In case when version not found 'false' is expected."),
        );
    }

    public function testGetControllerClassByOperationNamePositive()
    {
        $actualControllerClass = $this->_model->getControllerClassByOperationName('enterpriseCatalogProductGet');
        $message = 'Controller class was identified incorrectly by given operation.';
        $this->assertEquals('Enterprise_Catalog_Webapi_ProductController', $actualControllerClass, $message);
    }

    /**
     * @dataProvider dataProviderTestGetControllerClassByOperationNameNegative
     * @param string $operation
     * @param string $message
     */
    public function testGetControllerClassByOperationNameNegative($operation, $message)
    {
        $actualControllerClass = $this->_model->getControllerClassByOperationName($operation);
        $this->assertEquals(false, $actualControllerClass, $message);
    }

    public function dataProviderTestGetControllerClassByOperationNameNegative()
    {
        return array(
            array('customerMultiDeleteExcessiveSuffix', 'Excessive suffix is ignored.'),
            array('customerInvalid', "In case when operation not found 'false' is expected."),
        );
    }

    public function testGetControllerClassByOperationNameWithException()
    {
        $this->setExpectedException('LogicException',
            'Resource "resourceWithoutControllerAndModule" must have associated controller class.');
        $this->_model->getControllerClassByOperationName('resourceWithoutControllerAndModuleGet');
    }

    public function testGetModuleByOperationNamePositive()
    {
        $actualModuleName = $this->_model->getModuleNameByOperation('enterpriseCatalogProductGet');
        $message = 'Module name was identified incorrectly by given operation.';
        $this->assertEquals('Enterprise_Catalog', $actualModuleName, $message);
    }

    /**
     * @dataProvider dataProviderTestGetModuleByOperationNameNegative
     * @param string $operation
     * @param string $message
     */
    public function testGetModuleByOperationNameNegative($operation, $message)
    {
        $actualModuleName = $this->_model->getModuleNameByOperation($operation);
        $this->assertEquals(false, $actualModuleName, $message);
    }

    public function dataProviderTestGetModuleByOperationNameNegative()
    {
        return array(
            array('customerMultiDeleteExcessiveSuffix', 'Excessive suffix is ignored.'),
            array('customerInvalid', "In case when operation not found 'false' is expected."),
        );
    }

    public function testGetModuleByOperationNameWithException()
    {
        $this->setExpectedException('LogicException',
            'Resource "resourceWithoutControllerAndModule" must have module specified.');
        $this->_model->getModuleNameByOperation('resourceWithoutControllerAndModuleGet');
    }
}
