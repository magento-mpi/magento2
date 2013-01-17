<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Theme_Block_Adminhtml_System_Design_Tab_CssTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_modelName;

    /**
     * @var string
     */
    protected $_title;

    /**
     * @var array
     */
    protected $_mockEntities = array(
        'request'           => 'Mage_Core_Controller_Request_Http',
        'layout'            => 'Mage_Core_Model_Layout',
        'eventManager'      => 'Mage_Core_Model_Event_Manager',
        'urlBuilder'        => 'Mage_Backend_Model_Url',
        'translate'         => 'Mage_Core_Model_Translate',
        'cache'             => 'Mage_Core_Model_Cache',
        'designPackage'     => 'Mage_Core_Model_Design_Package',
        'session'           => 'Mage_Core_Model_Session',
        'storeConfig'       => 'Mage_Core_Model_Store_Config',
        'frontController'   => 'Mage_Core_Controller_Varien_Front',
        'helperFactory'     => 'Mage_Core_Model_Factory_Helper',
    );

    protected function setUp()
    {
        $this->_model = $this->getMock(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css',
            array('_getCurrentTheme'),
            $this->_prepareModelArguments(),
            '',
            true
        );
    }

    protected function _prepareModelArguments()
    {
        foreach ($this->_mockEntities as $propertyName => $className) {
            $mockObject = $this->getMock($className, array('translate'), array(), '', false);
            if ($propertyName === 'translate') {
                $translateCallback = function ($arguments) {
                    $result = '';
                    if (is_array($arguments) && current($arguments) instanceof Mage_Core_Model_Translate_Expr) {
                        /** @var Mage_Core_Model_Translate_Expr $expression */
                        $expression = array_shift($arguments);
                        $result = vsprintf($expression->getText(), $arguments);
                    }
                    return $result;
                };
                $mockObject->expects($this->any())->method('translate')
                    ->will($this->returnCallback($translateCallback));
            }
            $constructArguments[$propertyName] = $mockObject;
        }

        $uploaderService = $this->getMock('Mage_Theme_Model_Uploader_Service', array(), array(), '', false);

        $constructArguments['objectManager'] = Mage::getObjectManager();
        $constructArguments['uploaderService'] = $uploaderService;
        return $constructArguments;
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetUploadCssFileNote()
    {
        $method = self::getMethod('_getUploadCssFileNote');
        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = 'Allowed file types *.css.<br />';
        $expectedResult .= 'The file you upload will replace the existing custom.css file (shown below).<br />';
        $expectedResult .= sprintf(
            'Max file size to upload %sM',
            Mage::getObjectManager()->get('Magento_File_Size')->getMaxFileSizeInMb()
        );
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetAdditionalElementTypes()
    {
        $method = self::getMethod('_getAdditionalElementTypes');
        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = array(
            'links' => 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Links',
            'css_file' => 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File'
        );
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider getGroupedFilesProvider
     */
    public function testGetGroupedFiles($files, $expectedResult)
    {
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('getThemeTitle', 'getId'), array(), '', false);
        $themeMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $themeMock->expects($this->any())->method('getThemeTitle')->will($this->returnValue('test title'));

        $helperFactoryMock = $this->getMock(
            'Mage_Core_Model_Factory_Helper', array('get', 'urlEncode'), array(), '', false
        );
        $helperFactoryMock->expects($this->any())->method('get')->with($this->equalTo('Mage_Theme_Helper_Data'))
            ->will($this->returnSelf());

        $helperFactoryMock->expects($this->any())->method('urlEncode')->will($this->returnArgument(0));

        $constructArguments = $this->_prepareModelArguments();
        $constructArguments['helperFactory'] = $helperFactoryMock;
        $constructArguments['objectManager'] = $objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->setMethods(array('create', 'get', 'getOptions'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $collectionMock = $this->getMock(
            'Mage_Core_Model_Resource_Theme_Collection',
            get_class_methods('Mage_Core_Model_Resource_Theme_Collection'),
            array(),
            '',
            false
        );

        $collectionMock->expects($this->any())->method('getThemeByFullPath')->will($this->returnValue($themeMock));

        $configMock = $this->getMock(
            'Mage_Core_Model_Config',
            get_class_methods('Mage_Core_Model_Config'),
            array(),
            '',
            false
        );

        $configMock->expects($this->any())->method('getOptions')
            ->will($this->returnValue(Mage::getObjectManager()->get('Mage_Core_Model_Config')->getOptions()));

        $objectManagerMock->expects($this->any())->method('create')
            ->with($this->equalTo('Mage_Core_Model_Resource_Theme_Collection'))
            ->will($this->returnValue($collectionMock));

        $objectManagerMock->expects($this->any())->method('get')->with($this->equalTo('Mage_Core_Model_Config'))
            ->will($this->returnValue($configMock));

        $this->_model = $this->getMock(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css',
            array('getFiles', '_getCurrentTheme', 'getUrl'),
            $constructArguments,
            '',
            true
        );

        $this->_model->expects($this->once())->method('getFiles')->will($this->returnValue($files));
        $this->_model->expects($this->any())->method('_getCurrentTheme')->will($this->returnValue($themeMock));
        $this->_model->expects($this->any())->method('getUrl')->will($this->returnArgument(1));

        $method = self::getMethod('_getGroupedFiles');
        $result = $method->invokeArgs($this->_model, array());
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function getGroupedFilesProvider()
    {
        $options = Mage::getObjectManager()->get('Mage_Core_Model_Config')->getOptions();
        $designDir = $options->getDesignDir();
        $jsDir = $options->getJsDir();
        $codeDir = $options->getCodeDir();

        return array(
            array(array(), array()),
            array(
                array('mage/calendar.css' => str_replace('/', DIRECTORY_SEPARATOR,
                    $codeDir . '/pub/lib/mage/calendar.css')),
                array('Framework files' => array(
                    array(
                        'href' => array('theme_id' => 1, 'file' => 'mage/calendar.css'),
                        'label' => 'mage/calendar.css',
                        'title' => str_replace('/', DIRECTORY_SEPARATOR, $codeDir . '/pub/lib/mage/calendar.css'),
                        'delimiter' => '<br />'
            )))),
            array(
                array('Mage_Page::css/tabs.css' => str_replace('/', DIRECTORY_SEPARATOR,
                    $codeDir . '/core/Mage/Page/view/frontend/css/tabs.css')),
                array('Framework files' => array(
                    array(
                        'href' => array('theme_id' => 1, 'file' => 'Mage_Page::css/tabs.css'),
                        'label' => 'Mage_Page::css/tabs.css',
                        'title' => str_replace('/', DIRECTORY_SEPARATOR,
                            $codeDir . '/core/Mage/Page/view/frontend/css/tabs.css'),
                        'delimiter' => '<br />'
            )))),
            array(
                array('mage/calendar.css' => str_replace('/', DIRECTORY_SEPARATOR, $jsDir . '/mage/calendar.css')),
                array('Library files' => array(
                    array(
                        'href' => array('theme_id' => 1, 'file' => 'mage/calendar.css'),
                        'label' => 'mage/calendar.css',
                        'title' => str_replace('/', DIRECTORY_SEPARATOR, $jsDir . '/mage/calendar.css'),
                        'delimiter' => '<br />'
            )))),
            array(
                array('mage/calendar.css' => str_replace('/', DIRECTORY_SEPARATOR,
                    $designDir . '/frontend/default/demo/css/styles.css'),
                ),
                array('"test title" Theme files' => array(
                    array(
                        'href' => array('theme_id' => 1, 'file' => 'mage/calendar.css'),
                        'label' => 'mage/calendar.css',
                        'title' => str_replace('/', DIRECTORY_SEPARATOR,
                            $designDir . '/frontend/default/demo/css/styles.css'),
                        'delimiter' => '<br />'
            )))),
        );
    }

    /**
     * @dataProvider sortGroupFilesCallbackProvider
     */
    public function testSortGroupFilesCallback($firstGroup, $secondGroup, $expectedResult)
    {
        $method = self::getMethod('_sortGroupFilesCallback');
        $result = $method->invokeArgs($this->_model, array($firstGroup, $secondGroup));
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function sortGroupFilesCallbackProvider()
    {
        return array(
            array(
                array('label' => 'abcd'),
                array('label' => 'abc'),
                1
            ),
            array(
                array('label' => 'abc'),
                array('label' => 'abcd'),
                -1
            ),
            array(
                array('label' => 'abc'),
                array('label' => 'abc'),
                0
            ),
            array(
                array('label' => 'Mage_Core::abc'),
                array('label' => 'abc'),
                1
            ),
            array(
                array('label' => 'abc'),
                array('label' => 'Mage_Core::abc'),
                -1
            ),
            array(
                array('label' => 'Mage_Core::abc'),
                array('label' => 'Mage_Core::abcd'),
                -1
            ),
            array(
                array('label' => 'Mage_Core::abcd'),
                array('label' => 'Mage_Core::abc'),
                1
            ),
            array(
                array('label' => 'Mage_Core::abc'),
                array('label' => 'Mage_Core::abc'),
                0
            ),
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Invalid view file directory "xyz"
     */
    public function testGetGroupException()
    {
        $method = self::getMethod('_getGroup');
        $method->invokeArgs($this->_model, array('xyz'));
    }

    /**
     * @dataProvider getGroupProvider
     */
    public function testGetGroup($filename, $filePathForSearch, $themeId)
    {
        $this->_model = $this->getMock(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css',
            array('_getThemeByFilename'),
            $this->_prepareModelArguments(),
            '',
            true
        );

        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('getThemeId'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getThemeId')
            ->will($this->returnValue($themeId));

        $this->_model->expects($this->any())
            ->method('_getThemeByFilename')
            ->with($filePathForSearch)
            ->will($this->returnValue($themeMock));

        $method = self::getMethod('_getGroup');
        $result = $method->invokeArgs($this->_model, array($filename));

        $this->assertCount(2, $result);

        if ($filePathForSearch) {
            $this->assertSame($themeMock, $result[1]);
            $this->assertEquals(array($themeId, $themeMock), $result);
        } else {
            $this->assertEquals(array($themeId, null), $result);
        }
    }

    /**
     * @return array
     */
    public function getGroupProvider()
    {
        $options = Mage::getObjectManager()->create('Mage_Core_Model_Config')->getOptions();
        $designDir = $options->getDesignDir();
        $jsDir = $options->getJsDir();
        $codeDir = $options->getCodeDir();

        return array(
            array(
                $designDir . str_replace('/', DIRECTORY_SEPARATOR, 'a/b/c/f/file.xml'),
                str_replace('/', DIRECTORY_SEPARATOR, 'a/b/c/f/file.xml'),
                1
            ),
            array(
                $jsDir . str_replace('/', DIRECTORY_SEPARATOR, 'a/b/c/f/file.xml'),
                null,
                $jsDir
            ),
            array(
                $codeDir . str_replace('/', DIRECTORY_SEPARATOR, 'a/b/c/f/file.xml'),
                null,
                $codeDir
            ),
        );
    }

    /**
     * @dataProvider sortThemesByHierarchyCallbackProvider
     */
    public function testSortThemesByHierarchyCallback($firstThemeParentId, $parentOfParentTheme,
        $secondThemeId, $expectedResult
    ) {
        list($firstTheme, $secondTheme) = $this->_prepareThemesFroHierarchyCallback(
            $firstThemeParentId, $parentOfParentTheme, $secondThemeId
        );

        $method = self::getMethod('_sortThemesByHierarchyCallback');
        $result = $method->invokeArgs($this->_model, array($firstTheme, $secondTheme));
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function sortThemesByHierarchyCallbackProvider()
    {
        return array(
            array(1, null, 1, -1),
            array(1, $this->_getThemeMockFroHierarchyCallback(), 2, -1),
            array(1, null, 2, 1),
        );
    }

    /**
     * @param int $firstThemeParentId
     * @param Mage_Core_Model_Theme|null $parentOfParentTheme
     * @param int $secondThemeId
     * @return array
     */
    protected function _prepareThemesFroHierarchyCallback($firstThemeParentId, $parentOfParentTheme, $secondThemeId)
    {
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme', 'getId'), array(), '', false);

        $firstTheme = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme', 'getId'), array(), '', false);
        $firstTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));

        $firstTheme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(999));

        $parentTheme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($firstThemeParentId));

        $parentTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentOfParentTheme));

        $secondTheme = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
        $secondTheme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($secondThemeId));
        return array($firstTheme, $secondTheme);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getThemeMockFroHierarchyCallback()
    {
        $parentOfParentTheme = $this->getMock('Mage_Core_Model_Theme', array('getId', 'getParentTheme'),
            array(), '', false);
        $parentOfParentTheme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));
        $parentOfParentTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue(false));

        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getParentTheme'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentOfParentTheme));
        return $parentTheme;
    }

    /**
     * @param string $fileName
     * @param string $expectedResult
     * @dataProvider getThemeByFilenameProvider
     */
    public function testGetThemeByFilename($fileName, $expectedResult)
    {
        $constructArguments = $this->_prepareModelArguments();

        $constructArguments['objectManager'] = $objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $collectionMock = $this->getMock(
            'Mage_Core_Model_Resource_Theme_Collection',
            get_class_methods('Mage_Core_Model_Resource_Theme_Collection'),
            array(),
            '',
            false
        );

        $collectionMock->expects($this->atLeastOnce())
            ->method('getThemeByFullPath')
            ->will($this->returnArgument(0));

        $objectManagerMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->equalTo('Mage_Core_Model_Resource_Theme_Collection'))
            ->will($this->returnValue($collectionMock));

        $this->_model = $this->getMock(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css', array(), $constructArguments, '', true
        );

        $method = self::getMethod('_getThemeByFilename');
        $result = $method->invokeArgs($this->_model, array(str_replace('/', DIRECTORY_SEPARATOR, $fileName)));
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function getThemeByFilenameProvider()
    {
        return array(array('a/b/c/d/e.xml', 'a/b/c'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Theme path does not recognized
     */
    public function testGetThemeByFilenameException()
    {
        $method = self::getMethod('_getThemeByFilename');
        $method->invokeArgs($this->_model, array('a'));
    }

    public function testGetGroupLabels()
    {
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array('getThemeId', 'getThemeTitle'), array(), '', false);
        $themeModel->expects($this->any())
            ->method('getThemeId')
            ->will($this->returnValue(1));

        $themeModel->expects($this->any())
            ->method('getThemeTitle')
            ->will($this->returnValue('title'));

        $method = self::getMethod('_getGroupLabels');
        $result = $method->invokeArgs($this->_model, array(array($themeModel)));

        $this->assertContains('Library files', $result);
        $this->assertContains('Framework files', $result);
        $this->assertContains('"title" Theme files', $result);
        $this->assertArrayHasKey(1, $result);
    }

    /**
     * @param array $groups
     * @param array $order
     * @param array $expectedResult
     * @dataProvider sortArrayByArrayProvider
     */
    public function testSortArrayByArray($groups, $order, $expectedResult)
    {
        $method = self::getMethod('_sortArrayByArray');
        $result = $method->invokeArgs($this->_model, array($groups, $order));
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function sortArrayByArrayProvider()
    {
        return array(
            array(
                array('b' => 'item2', 'a' => 'item1', 'c' => 'item3'),
                array('a', 'b', 'c'),
                array('a' => 'item1', 'b' => 'item2', 'c' => 'item3')
            ),
            array(
                array('x' => 'itemX'),
                array('a', 'b', 'c'),
                array('x' => 'itemX')
            ),
            array(
                array('b' => 'item2', 'a' => 'item1', 'c' => 'item3', 'd' => 'item4', 'e' => 'item5'),
                array('d', 'e'),
                array('d' => 'item4', 'e' => 'item5', 'b' => 'item2', 'a' => 'item1', 'c' => 'item3'),
            ),
        );
    }

    public function testGetTabLabel()
    {
        $this->assertEquals('CSS Editor', $this->_model->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->assertEquals('CSS Editor', $this->_model->getTabTitle());
    }

    /**
     * @dataProvider canShowTabDataProvider
     * @param bool $isVirtual
     * @param int $themeId
     * @param bool $result
     */
    public function testCanShowTab($isVirtual, $themeId, $result)
    {
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('isVirtual', 'getId'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('isVirtual')
            ->will($this->returnValue($isVirtual));

        $themeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($themeId));

        $this->_model->expects($this->any())
            ->method('_getCurrentTheme')
            ->will($this->returnValue($themeMock));

        if ($result === true) {
            $this->assertTrue($this->_model->canShowTab());
        } else {
            $this->assertFalse($this->_model->canShowTab());
        }
    }

    /**
     * @return array
     */
    public function canShowTabDataProvider()
    {
        return array(
            array(true, 1, true),
            array(true, 0, false),
            array(false, 1, false),
        );
    }

    public function testIsHidden()
    {
        $this->assertFalse($this->_model->isHidden());
    }

    /**
     * @param string $name
     * @return ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
