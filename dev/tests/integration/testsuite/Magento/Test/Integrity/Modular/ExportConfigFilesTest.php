<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class ExportConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Export\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $filesystem \Magento\Filesystem */
        $filesystem = $objectManager->get('Magento\Filesystem');
        $modulesDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::MODULES);
        $fileIteratorFactory = $objectManager->get('Magento\Config\FileIteratorFactory');
        $xmlFiles = $fileIteratorFactory->create(
            $modulesDirectory,
            $modulesDirectory->search('/*/*/etc/{*/export.xml,export.xml}')
        );

        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')
            ->will($this->returnValue(true));
        $fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')
            ->will($this->returnValue($xmlFiles));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_model = $objectManager->create('Magento\ImportExport\Model\Export\Config\Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
        ));
    }

    public function testExportXmlFiles()
    {
        $this->_model->read('global');
    }
}
