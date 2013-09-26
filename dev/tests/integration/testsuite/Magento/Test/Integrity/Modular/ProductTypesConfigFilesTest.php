<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class ProductTypesConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\Config\Reader
     */
    protected $_model;

    protected function setUp()
    {
        // List of all available product_types.xml
        $xmlFiles = \Magento\TestFramework\Utility\Files::init()->getConfigFiles(
            '{*/product_types.xml,product_types.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );
        $fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento\Catalog\Model\ProductTypes\Config\Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
        ));
    }

    public function testProductTypesXmlFiles()
    {
        $this->_model->read('global');
    }
}
