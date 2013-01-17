<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme js file model
 */
class Mage_Core_Model_Theme_Files_JsTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareFileName()
    {
        $fileName = 'js_file.js';

        /** @var $jsFile Mage_Core_Model_Theme_Files_Js */
        $jsFile = $this->getMock(
            'Mage_Core_Model_Theme_Files_Js', array('_getThemeFileByName', 'getId'), array(), '', false
        );

        $jsFile->expects($this->any())
            ->method('_getThemeFileByName')
            ->will($this->returnValue($jsFile));

        $jsFile->expects($this->at(1))
            ->method('getId')
            ->will($this->returnValue(true));

        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);

        $prepareFileName = new ReflectionMethod($jsFile, '_prepareFileName');
        $prepareFileName->setAccessible(true);
        $result = $prepareFileName->invoke($jsFile, $themeModel, $fileName);
        $this->assertEquals('js_file_1.js', $result);
    }

    public function testSaveDataWithoutData()
    {
        $filesModel = $this->_getMockThemeFile();
        $themeModel = $this->_getMockThemeModel();

        $modelJsFile = $this->getMock(
            'Mage_Core_Model_Theme_Files_Js',
            array('_delete', '_save'),
            array($filesModel)
        );

        $modelJsFile->expects($this->never())->method('_save');
        $modelJsFile->expects($this->never())->method('_delete');
        $modelJsFile->saveData($themeModel);
    }

    public function testSaveDataWithDelete()
    {
        $jsFilesIdForDelete = array(1, 2, 4, 5);
        $themeJsFilesId = array(1, 2, 3, 4, 5, 6);

        $filesModel = $this->_getMockThemeFile();
        $themeModel = $this->_getMockThemeModel();

        $filesCollection = array();
        foreach ($themeJsFilesId as $fileId) {
            $files = $this->_getMockThemeFile();
            $files->expects(in_array($fileId, $jsFilesIdForDelete) ? $this->once() : $this->never())->method('delete');
            $files->expects($this->once())->method('getId')->will($this->returnValue($fileId));
            $filesCollection[] = $files;
        }

        $modelJsFile = $this->getMock(
            'Mage_Core_Model_Theme_Files_Js',
            array('getCollectionByTheme', '_save'),
            array($filesModel)
        );

        $modelJsFile->expects($this->never())->method('_save');
        $modelJsFile->expects($this->once())
            ->method('getCollectionByTheme')
            ->with($themeModel)
            ->will($this->returnValue($filesCollection));

        $modelJsFile->setDataForDelete($jsFilesIdForDelete);
        $modelJsFile->saveData($themeModel);
    }

    /**
     * @param int $return
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Theme
     */
    protected function _getMockThemeModel($return = null)
    {
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
        $themeModel->expects($return ? $this->once() : $this->never())
            ->method('getId')
            ->will($this->returnValue($return));
        return $themeModel;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Theme_Files
     */
    protected function _getMockThemeFile()
    {
        $filesModel = $this->getMock('Mage_Core_Model_Theme_Files', array(
            'load',
            'getId',
            'getThemeId',
            'setIsTemporary',
            'save',
            'delete'
        ), array(), '', false);
        return $filesModel;
    }
}
