<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test of file abstract service
 */
class Magento_Core_Model_Theme_Customization_FileAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder
     */
    protected $_modelBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customizationPath;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    protected function setUp()
    {
        $this->_customizationPath = $this->getMock('Magento\Core\Model\Theme\Customization\Path',
            array(), array(), '', false);
        $this->_fileFactory = $this->getMock(
            'Magento\Core\Model\Theme\FileFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);

        $this->_modelBuilder = $this->getMockBuilder('Magento\Core\Model\Theme\Customization\FileAbstract')
            ->setMethods(array('getType', 'getContentType'))
            ->setConstructorArgs(array($this->_customizationPath, $this->_fileFactory, $this->_filesystem));
    }

    protected function tearDown()
    {
        $this->_customizationPath = null;
        $this->_fileFactory = null;
        $this->_filesystem = null;
        $this->_modelBuilder = null;
    }

    /**
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::__construct
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::create
     */
    public function testCreate()
    {
        $model = $this->_modelBuilder->getMock();
        $file = $this->getMock('Magento\Core\Model\Theme\File', array(), array(), '', false);
        $file->expects($this->once())->method('setCustomizationService')->with($model);
        $this->_fileFactory->expects($this->once())->method('create')->will($this->returnValue($file));
        /** @var $model \Magento\Core\Model\Theme\Customization\FileAbstract */
        $this->assertEquals($file, $model->create());
    }

    /**
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::getFullPath
     */
    public function testGetFullPath()
    {
        $model = $this->_modelBuilder->getMock();
        $theme = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $file = $this->getMock('Magento\Core\Model\Theme\File', array(), array(), '', false);

        $file->expects($this->any())->method('getTheme')->will($this->returnValue($theme));
        $file->expects($this->once())->method('getData')->with('file_path')->will($this->returnValue('file.path'));

        $this->_customizationPath->expects($this->once())->method('getCustomizationPath')
            ->will($this->returnValue('/path'));

        /** @var $model \Magento\Core\Model\Theme\Customization\FileAbstract */
        /** @var $file \Magento\Core\Model\Theme\File */
        $this->assertEquals('/path' . DIRECTORY_SEPARATOR . 'file.path', $model->getFullPath($file));
    }

    /**
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::prepareFile
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::_prepareFileName
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::_prepareFilePath
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::_prepareSortOrder
     * @dataProvider getTestContent
     */
    public function testPrepareFile($type, $fileContent, $expectedContent, $existedFiles)
    {
        $model = $this->_modelBuilder->getMock();
        $model->expects($this->any())->method('getType')->will($this->returnValue($type));
        $model->expects($this->any())->method('getContentType')->will($this->returnValue($type));

        $files = array();
        foreach ($existedFiles as $fileData) {
            $file = $this->getMock('Magento\Core\Model\Theme\File', array('save'), array(), '', false);
            $file->setData($fileData);
            $files[] = $file;
        }
        $customization = $this->getMock('Magento\Core\Model\Theme\Customization', array(), array(), '', false);
        $customization->expects($this->atLeastOnce())->method('getFilesByType')->with($type)
            ->will($this->returnValue($files));

        $theme = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $theme->expects($this->any())->method('getCustomization')->will($this->returnValue($customization));

        $file = $this->getMock('Magento\Core\Model\Theme\File', array('getTheme', 'save'), array(), '', false);
        $file->expects($this->any())->method('getTheme')->will($this->returnValue($theme));
        $file->setData($fileContent);

        /** @var $model \Magento\Core\Model\Theme\Customization\FileAbstract */
        /** @var $file \Magento\Core\Model\Theme\File */
        $model->prepareFile($file);
        $this->assertEquals($expectedContent, $file->getData());
    }

    /**
     * @return array
     */
    public function getTestContent()
    {
        return array(
            'first_condition' => array(
                'type' => 'css',
                'fileContent' => array(
                    'file_name'  => 'test.css',
                    'content'    => 'test content',
                    'sort_order' => 1
                ),
                'expectedContent' => array(
                    'file_type'  => 'css',
                    'file_name'  => 'test_1.css',
                    'file_path'  => 'css/test_1.css',
                    'content'    => 'test content',
                    'sort_order' => 2
                ),
                'existedFiles' => array(
                    array(
                        'id'         => 1,
                        'file_path'  => 'css/test.css',
                        'content'    => 'test content',
                        'sort_order' => 1
                    )
                )
            ),

            'second_condition' => array(
                'type' => 'js',
                'fileContent' => array(
                    'file_name'  => 'test.js',
                    'content'    => 'test content',
                    'sort_order' => 1
                ),
                'expectedContent' => array(
                    'file_type'  => 'js',
                    'file_name'  => 'test_3.js',
                    'file_path'  => 'js/test_3.js',
                    'content'    => 'test content',
                    'sort_order' => 12
                ),
                'existedFiles' => array(
                    array(
                        'id'         => 1,
                        'file_path'  => 'js/test.js',
                        'content'    => 'test content',
                        'sort_order' => 3
                    ),
                    array(
                        'id'         => 2,
                        'file_path'  => 'js/test_1.js',
                        'content'    => 'test content',
                        'sort_order' => 5
                    ),
                    array(
                        'id'         => 3,
                        'file_path'  => 'js/test_2.js',
                        'content'    => 'test content',
                        'sort_order' => 7
                    ),
                    array(
                        'id'         => 4,
                        'file_path'  => 'js/test_4.js',
                        'content'    => 'test content',
                        'sort_order' => 9
                    ),
                    array(
                        'id'         => 5,
                        'file_path'  => 'js/test_5.js',
                        'content'    => 'test content',
                        'sort_order' => 11
                    )
                )
            )
        );
    }

    /**
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::save
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::_saveFileContent
     */
    public function testSave()
    {
        $model = $this->_modelBuilder->setMethods(array('getFullPath', 'getType', 'getContentType'))->getMock();

        $file = $this->getMock('Magento\Core\Model\Theme\File', null, array(), '', false);
        $file->setData(array(
            'file_type'  => 'js',
            'file_name'  => 'test_3.js',
            'file_path'  => 'js/test_3.js',
            'content'    => 'test content',
            'sort_order' => 12
        ));
        $model->expects($this->once())->method('getFullPath')->with($file)->will($this->returnValue('test_path'));
        $this->_filesystem->expects($this->once())->method('delete')->with('test_path');
        $this->_filesystem->expects($this->once())->method('setIsAllowCreateDirectories')->with(true)
            ->will($this->returnSelf());
        $this->_filesystem->expects($this->once())->method('write')->with('test_path', 'test content');
        /** @var $model \Magento\Core\Model\Theme\Customization\FileAbstract */
        /** @var $file \Magento\Core\Model\Theme\File */
        $model->save($file);
    }

    /**
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::delete
     * @covers \Magento\Core\Model\Theme\Customization\FileAbstract::_deleteFileContent
     */
    public function testDelete()
    {
        $model = $this->_modelBuilder->setMethods(array('getFullPath', 'getType', 'getContentType'))->getMock();
        $file = $this->getMock('Magento\Core\Model\Theme\File', null, array(), '', false);
        $file->setData(array(
            'file_type'  => 'js',
            'file_name'  => 'test_3.js',
            'file_path'  => 'js/test_3.js',
            'content'    => 'test content',
            'sort_order' => 12
        ));
        $this->_filesystem->expects($this->once())->method('has')->with('test_path')->will($this->returnValue(true));
        $this->_filesystem->expects($this->once())->method('delete')->with('test_path');
        $model->expects($this->once())->method('getFullPath')->with($file)->will($this->returnValue('test_path'));
        /** @var $model \Magento\Core\Model\Theme\Customization\FileAbstract */
        /** @var $file \Magento\Core\Model\Theme\File */
        $model->delete($file);
    }
}
