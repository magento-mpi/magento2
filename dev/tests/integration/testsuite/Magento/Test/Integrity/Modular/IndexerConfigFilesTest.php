<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class IndexerConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\Model\Indexer\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        // List of all available import.xml
        $xmlFiles = \Magento\TestFramework\Utility\Files::init()->getConfigFiles(
            '{*/indexers.xml,indexers.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );

        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')
            ->will($this->returnValue(true));
        $fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_model = $objectManager->create('Magento\Index\Model\Indexer\Config\Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
        ));
    }

    public function testImportXmlFiles()
    {
        $this->_model->read('global');
    }
}
