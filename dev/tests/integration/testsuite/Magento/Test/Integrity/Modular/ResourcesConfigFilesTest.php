<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class ResourcesConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Config\Reader
     */
    protected $_model;

    protected function setUp()
    {
        // List of all resources.xml
        $xmlFiles = \Magento\TestFramework\Utility\Files::init()->getConfigFiles(
            '{*/resources.xml,resources.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );
        $fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));
        $localConfigMock = $this->getMock('Magento\Core\Model\Config\Local', array(), array(), '', false);
        $localConfigMock->expects($this->any())->method('getConfiguration')->will($this->returnValue(array()));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento\Core\Model\Resource\Config\Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
            'localConfig' => $localConfigMock,
        ));
    }

    public function testResourcesXmlFiles()
    {
        $this->_model->read('global');
    }
}
