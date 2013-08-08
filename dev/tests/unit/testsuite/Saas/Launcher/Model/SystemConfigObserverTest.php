<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_SystemConfigObserverTest extends PHPUnit_Framework_TestCase
{
    public function testHandleSystemConfigChange()
    {
        $model = $this->_getModelForHandleSystemConfigChangeTest();
        $observer = new Magento_Event_Observer(array('event' => new Magento_Event(array('section' => 'test'))));
        $model->handleSystemConfigChange($observer);
    }

    protected function _getModelForHandleSystemConfigChangeTest()
    {
        // Mock tile associated with page and related tile collection
        $tile = $this->getMock(
            'Saas_Launcher_Model_Tile',
            array('getStateResolver', 'setState', 'save'),
            array(),
            '',
            false
        );

        $tile->expects($this->once())
            ->method('getStateResolver')
            ->will($this->returnValue(new Saas_Launcher_Model_Tile_StateResolverStub()));
        // stub state resolver always return COMPLETE state so observer has to set this state and save the tile
        $tile->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(Saas_Launcher_Model_Tile::STATE_COMPLETE));
        $tile->expects($this->once())
            ->method('save');

        $tileCollection = $this->getMock(
            'Saas_Launcher_Model_Resource_Tile_Collection',
            array('load', 'getItems', 'getIterator'),
            array(),
            '',
            false
        );
        $tileCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array($tile))));

        // Mock page
        $page = $this->getMock(
            'Saas_Launcher_Model_Page',
            array(),
            array(),
            '',
            false
        );
        $page->expects($this->once())
            ->method('getTiles')
            ->will($this->returnValue($tileCollection));

        $pageCollection = $this->getMock(
            'Saas_Launcher_Model_Resource_Page_Collection',
            array('load', 'getItems', 'getIterator'),
            array(),
            '',
            false
        );
        $pageCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array($page))));

        $applicationConfig = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        return new Saas_Launcher_Model_SystemConfigObserver($applicationConfig, $pageCollection);
    }
}
