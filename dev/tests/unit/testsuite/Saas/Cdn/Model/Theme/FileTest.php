<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Cdn_Model_Theme_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Filesystem_Adapter_Local
     */
    protected $_filesystemAdapterMock;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirMock;

    /**
     * @var Saas_Cdn_Model_Provider_Edgecast
     */
    protected $_cdnMock;

    public function setUp()
    {
        $this->_filesystemAdapterMock = $this->getMock('Magento_Filesystem_Adapter_Local', array(), array(), '', false);
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_cdnMock = $this->getMock('Saas_Cdn_Model_Provider_Edgecast', array(), array(), '', false);
    }

    /**
     * Check if CDN clearing calls when we delete media file from tracked dir
     *
     * @test
     */
    public function deleteFileFromTrackedDir()
    {
        $workingDirectory = 'dir/';
        $fileName = $workingDirectory . 'file';

        $map = array(
            array(Mage_Core_Model_Dir::MEDIA, $workingDirectory),
            array(Mage_Core_Model_Dir::STATIC_VIEW, 'static_dir'),
        );
        $this->_dirMock->expects($this->any())->method('getDir')->will($this->returnValueMap($map));

        $this->_cdnMock->expects($this->once())->method('deleteFile')->with($this->equalTo($fileName));

        $saasFilesystem = new Saas_Cdn_Model_Filesystem(
            $this->_filesystemAdapterMock,
            $this->_dirMock,
            $this->_cdnMock
        );

        $saasFilesystem->delete($fileName, $workingDirectory);

    }

    /**
     * Check if CDN clearing not calls when we delete media file from non tracked dir
     *
     * @test
     */
    public function deleteFileFromUntrackedDir()
    {
        $workingDirectory = 'dir/';
        $fileName = $workingDirectory . 'file';

        $map = array(
            array(Mage_Core_Model_Dir::MEDIA, 'non' . $workingDirectory),
            array(Mage_Core_Model_Dir::STATIC_VIEW, 'static_dir'),
        );
        $this->_dirMock->expects($this->any())->method('getDir')->will($this->returnValueMap($map));

        $this->_cdnMock->expects($this->never())->method('deleteFile');

        $saasFilesystem = new Saas_Cdn_Model_Filesystem(
            $this->_filesystemAdapterMock,
            $this->_dirMock,
            $this->_cdnMock
        );

        $saasFilesystem->delete($fileName, $workingDirectory);

    }

    public function tearDown()
    {
        $this->_filesystemAdapterMock = null;
        $this->_dirMock = null;
        $this->_cdnMock = null;
    }
}