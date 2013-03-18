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

        $themeFactory = $this->_getThemeFactory();
        $layoutLink = $this->_getLayoutLink();
        $layoutUpdate = $this->_getLayoutUpdate();

        $model = new Mage_Core_Model_Theme_Copy_VirtualToStaging($themeFactory, $layoutLink, $layoutUpdate);

        // Create "source" theme
        /** @var $virtualTheme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $virtualTheme = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $virtualTheme->setData($data);

        // Run tested method
        $stagingTheme = $model->copy($virtualTheme);

        // Test if result is what was expected
        $skipData = array('id');
        $result = $stagingTheme->getData();
        foreach ($expectedData as $key => $value) {
            if (!in_array($key, $skipData)) {
                $this->assertEquals($expectedData[$key], $result[$key]);
            }
        }
    }

    /**
     * @param string $type
     * @dataProvider testCopyInvalidThemeDataProvider
     */
    public function testCopyInvalidTheme($type)
    {
        $themeFactory = $this->_getThemeFactory();
        $layoutUpdate = $this->_getLayoutUpdate();

        /** @var $layoutLink Mage_Core_Model_Layout_Link|PHPUnit_Framework_MockObject_MockObject */
        $layoutLink = $this->getMock('Mage_Core_Model_Layout_Link', null, array(), '', false);

        $model = new Mage_Core_Model_Theme_Copy_VirtualToStaging($themeFactory, $layoutLink, $layoutUpdate);

        $data = array('type' => $type);

        // Create "source" theme
        /** @var $theme Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject */
        $theme = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $theme->setData($data);

        // Run tested method
        try {
            $model->copy($theme);
            $this->fail(sprintf(
                'Attempt to copy theme of type "%s" should generate an exception, but it didn\'t',
                $theme->getType()
            ));
        } catch (Mage_Core_Exception $e) {
            // Exception means test is passed
        }
    }

    public function testCopyInvalidThemeDataProvider()
    {
        return array(
            array(null),
            array(''),
            array(Mage_Core_Model_Theme::TYPE_PHYSICAL),
            array(Mage_Core_Model_Theme::TYPE_STAGING),
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

    /**
     * @return Mage_Core_Model_Layout_Link|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getLayoutLink()
    {
        /** @var $collection Mage_Core_Model_Resource_Layout_Link_Collection|PHPUnit_Framework_MockObject_MockObject */
        $collection = $this->getMock('Mage_Core_Model_Resource_Layout_Link_Collection', array(
            'addTemporaryFilter', 'addFieldToFilter', 'load'
        ), array(), '', false);
        $collection->expects($this->any())
            ->method('addTemporaryFilter')
            ->will($this->returnSelf());

        $sourceCollection = clone $collection;
        $targetCollection = clone $collection;

        /** @var $layoutLink Mage_Core_Model_Layout_Link|PHPUnit_Framework_MockObject_MockObject */
        $layoutLink = $this->getMock('Mage_Core_Model_Layout_Link', array('getCollection'), array(), '', false);
        $layoutLink->expects($this->at(0))
            ->method('getCollection')
            ->will($this->returnValue($sourceCollection));
        $layoutLink->expects($this->at(1))
            ->method('getCollection')
            ->will($this->returnValue($targetCollection));

        return $layoutLink;
    }

    /**
     * @return Mage_Core_Model_Layout_Update|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getLayoutUpdate()
    {
        /** @var $layoutUpdate Mage_Core_Model_Layout_Update|PHPUnit_Framework_MockObject_MockObject */
        $layoutUpdate = $this->getMock('Mage_Core_Model_Layout_Update', array('getCollection'), array(), '', false);

        return $layoutUpdate;
    }
}
