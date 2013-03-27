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
 * Test theme model
 */
class Mage_Core_Model_Theme_Copy_VirtualToStagingTest extends PHPUnit_Framework_TestCase
{
    public function testCopy()
    {
        //test ability to copy theme which is not virtual

        $data = array(
            'id'                   => 123,
            'parent_id'            => '',
            'theme_path'           => '',
            'theme_version'        => '1.2.3.4',
            'theme_title'          => 'Some Test Theme',
            'preview_image'        => 'image.gif',
            'magento_version_from' => '0.0.0.1',
            'magento_version_to'   => '0.0.0.2',
            'is_featured'          => 0,
            'area'                 => Mage_Core_Model_App_Area::AREA_FRONTEND,
            'type'                 => Mage_Core_Model_Theme::TYPE_VIRTUAL
        );
        $expectedData = $data;
        $expectedData['id'] = null;
        $expectedData['parent_id'] = $data['id'];
        $expectedData['theme_title'] = sprintf('%s - Staging', $data['theme_title']);
        $expectedData['type'] = Mage_Core_Model_Theme::TYPE_STAGING;

        $model = new Mage_Core_Model_Theme_Copy_VirtualToStaging($this->_getThemeFactory());

        // Create "source" theme
        /** @var $virtualTheme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $virtualTheme = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $virtualTheme->setData($data);

        // Run tested method
        $stagingTheme = $model->copy($virtualTheme);

        // Test if result is what was expected
        $skipData = array('id');
        $result = $stagingTheme->getData();
        foreach (array_keys($expectedData) as $key) {
            if (!in_array($key, $skipData)) {
                $this->assertEquals($expectedData[$key], $result[$key]);
            }
        }
    }

    /**
     * @param string $type
     * @param string $expectedExceptionMsg
     * @dataProvider testCopyInvalidThemeDataProvider
     */
    public function testCopyInvalidTheme($type, $expectedExceptionMsg)
    {
        $this->setExpectedException('Mage_Core_Exception', $expectedExceptionMsg);

        $model = new Mage_Core_Model_Theme_Copy_VirtualToStaging($this->_getThemeFactory());

        $data = array('type' => $type);

        // Create "source" theme
        /** @var $theme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $theme = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $theme->setData($data);

        $model->copy($theme);
    }

    public function testCopyInvalidThemeDataProvider()
    {
        return array(
            'null theme type'   => array(null, 'Invalid theme of type ""'),
            'empty theme type'  => array('', 'Invalid theme of type ""'),
            'physical theme'    => array(Mage_Core_Model_Theme::TYPE_PHYSICAL, 'Invalid theme of type "0"'),
            'staging theme'     => array(Mage_Core_Model_Theme::TYPE_STAGING, 'Invalid theme of type "2"'),
        );
    }

    /**
     * @return Mage_Core_Model_Theme_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    public function _getThemeFactory()
    {
        /** @var $stagingTheme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $stagingTheme = $this->getMock('Mage_Core_Model_Theme', array('save'), array(), '', false);

        /** @var $themeFactory Mage_Core_Model_Theme_Factory|PHPUnit_Framework_MockObject_MockObject */
        $themeFactory = $this->getMock('Mage_Core_Model_Theme_Factory', array('create'), array(), '', false);
        $themeFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($stagingTheme));

        return $themeFactory;
    }
}
