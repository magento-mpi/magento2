<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once __DIR__ . '/../../../../../../../../tools/Magento/Tools/View/Generator/CopyRule.php';

class Magento_Test_Tools_View_Generator_CopyRuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Generator_CopyRule
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallbackRule;

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento_Filesystem', array('searchKeys', 'isDirectory'), array(
            $this->getMockForAbstractClass('Magento_Filesystem_AdapterInterface')
        ));
        $this->_themeCollection = $this->getMock('Magento_Core_Model_Theme_Collection', array('isLoaded'), array(
            $this->_filesystem,
            new Magento_Core_Model_Dir(__DIR__)
        ));
        $this->_themeCollection->expects($this->any())->method('isLoaded')->will($this->returnValue(true));
        $this->_fallbackRule = $this->getMockForAbstractClass('Magento_Core_Model_Design_Fallback_Rule_RuleInterface');
        $this->_object = new Generator_CopyRule($this->_filesystem, $this->_themeCollection, $this->_fallbackRule);
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_filesystem = null;
        $this->_themeCollection = null;
        $this->_fallbackRule = null;
    }

    /**
     * @param array $fixtureThemes
     * @param array $patternDirMap
     * @param array $filesystemGlobMap
     * @param array $expectedResult
     * @dataProvider getCopyRulesDataProvider
     */
    public function testGetCopyRules(
        array $fixtureThemes, array $patternDirMap, array $filesystemGlobMap, array $expectedResult
    ) {
        foreach ($fixtureThemes as $theme) {
            $this->_themeCollection->addItem($theme);
        }
        $this->_fallbackRule
            ->expects($this->atLeastOnce())
            ->method('getPatternDirs')
            ->will($this->returnValueMap($patternDirMap))
        ;
        $this->_filesystem
            ->expects($this->atLeastOnce())
            ->method('searchKeys')
            ->will($this->returnValueMap($filesystemGlobMap))
        ;
        $this->_filesystem
            ->expects($this->atLeastOnce())
            ->method('isDirectory')
            ->will($this->returnValue(true))
        ;
        $this->assertEquals($expectedResult, $this->_object->getCopyRules());
    }

    public function getCopyRulesDataProvider()
    {
        $fixture = require __DIR__ . '/_files/fixture_themes.php';

        $patternDirMap = array();
        $filesystemGlobMap = array();
        foreach ($fixture as $fixtureInfo) {
            $patternDirMap = array_merge($patternDirMap, $fixtureInfo['pattern_dir_map']);
            $filesystemGlobMap = array_merge($filesystemGlobMap, $fixtureInfo['filesystem_glob_map']);
        }

        return array(
            'reverse fallback traversal' => array(
                array($fixture['theme_customizing_one_module']['theme']),
                $patternDirMap,
                $filesystemGlobMap,
                $fixture['theme_customizing_one_module']['expected_result'],
            ),
            'themes in the same area' => array(
                array(
                    $fixture['theme_customizing_one_module']['theme'],
                    $fixture['theme_customizing_two_modules']['theme']
                ),
                $patternDirMap,
                $filesystemGlobMap,
                array_merge(
                    $fixture['theme_customizing_one_module']['expected_result'],
                    $fixture['theme_customizing_two_modules']['expected_result']
                ),
            ),
            'themes in different areas' => array(
                array(
                    $fixture['theme_customizing_one_module']['theme'],
                    $fixture['theme_customizing_no_modules']['theme']
                ),
                $patternDirMap,
                $filesystemGlobMap,
                array_merge(
                    $fixture['theme_customizing_one_module']['expected_result'],
                    $fixture['theme_customizing_no_modules']['expected_result']
                ),
            ),
            'mixed directory separators in fallback pattern' => array(
                array($fixture['fallback_pattern_mixing_slashes']['theme']),
                $patternDirMap,
                $filesystemGlobMap,
                $fixture['fallback_pattern_mixing_slashes']['expected_result'],
            ),
        );
    }
}
