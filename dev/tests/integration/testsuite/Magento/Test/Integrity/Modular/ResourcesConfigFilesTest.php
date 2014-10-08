<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

use Magento\Framework\App\Filesystem\DirectoryList;

class ResourcesConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Resource\Config\Reader
     */
    protected $_model;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $filesystem \Magento\Framework\App\Filesystem */
        $filesystem = $objectManager->get('Magento\Framework\App\Filesystem');
        $modulesDirectory = $filesystem->getDirectoryRead(DirectoryList::MODULES);
        $fileIteratorFactory = $objectManager->get('Magento\Framework\Config\FileIteratorFactory');
        $xmlFiles = $fileIteratorFactory->create(
            $modulesDirectory,
            $modulesDirectory->search('/*/*/etc/{*/resources.xml,resources.xml}')
        );

        $fileResolverMock = $this->getMock('Magento\Framework\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $validationStateMock = $this->getMock('Magento\Framework\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));
        $localConfigMock = $this->getMock('Magento\Framework\App\Arguments', array(), array(), '', false);
        $localConfigMock->expects($this->any())->method('getConfiguration')->will($this->returnValue(array()));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->create(
            'Magento\Framework\App\Resource\Config\Reader',
            array(
                'fileResolver' => $fileResolverMock,
                'validationState' => $validationStateMock,
                'localConfig' => $localConfigMock
            )
        );
    }

    public function testResourcesXmlFiles()
    {
        $this->_model->read('global');
    }
}
