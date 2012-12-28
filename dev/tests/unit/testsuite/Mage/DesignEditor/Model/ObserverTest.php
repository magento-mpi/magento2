<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test number of days after which layout will be removed
     */
    const TEST_DAYS_TO_EXPIRE = 5;

    /**
     * @var Mage_DesignEditor_Model_Observer
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param int $themeId
     * @dataProvider setThemeDataProvider
     */
    public function testSetTheme($themeId)
    {
        /** @var $session Mage_Backend_Model_Session */
        $session = $this->getMock('Mage_Backend_Model_Session', null, array(), '', false);
        $session->setData('theme_id', $themeId);

        $design = $this->getMock('Mage_Core_Model_Design_Package', array('setDesignTheme'), array(), '', false);
        if ($themeId !== null) {
            $design->expects($this->once())
                ->method('setDesignTheme')
                ->with($themeId);
        } else {
            $design->expects($this->never())
                ->method('setDesignTheme');
        }

        /** @var $objectManager Magento_ObjectManager */
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        /** @var $helper Mage_DesignEditor_Helper_Data */
        $helper = $this->getMock('Mage_DesignEditor_Helper_Data', array(), array(), '', false);

        $this->_model = new Mage_DesignEditor_Model_Observer($objectManager, $session, $design, $helper);
        $this->_model->setTheme();
    }

    /**
     * @return array
     */
    public function setThemeDataProvider()
    {
        return array(
            'no theme id'      => array('$themeId' => null),
            'correct theme id' => array('$themeId' => 1),
        );
    }

    public function testClearLayoutUpdates()
    {
        // important mocks
        $helper = $this->getMock('Mage_DesignEditor_Helper_Data', array('getDaysToExpire'), array(), '', false);
        $helper->expects($this->once())
            ->method('getDaysToExpire')
            ->will($this->returnValue(self::TEST_DAYS_TO_EXPIRE));

        /** @var $linkCollection Mage_Core_Model_Resource_Layout_Link_Collection */
        $linkCollection = $this->getMock(
            'Mage_Core_Model_Resource_Layout_Link_Collection',
            array('addTemporaryFilter', 'addUpdatedDaysBeforeFilter', 'load'),
            array(),
            '',
            false
        );
        $linkCollection->expects($this->once())
            ->method('addTemporaryFilter')
            ->with(true)
            ->will($this->returnSelf());
        $linkCollection->expects($this->once())
            ->method('addUpdatedDaysBeforeFilter')
            ->with(self::TEST_DAYS_TO_EXPIRE)
            ->will($this->returnSelf());
        for ($i = 0; $i < 3; $i++) {
            $link = $this->getMock('Mage_Core_Model_Layout_Link', array('delete'), array(), '', false);
            $link->expects($this->once())
                ->method('delete');
            $linkCollection->addItem($link);
        }

        /** @var $layoutCollection Mage_Core_Model_Resource_Layout_Update_Collection */
        $layoutCollection = $this->getMock(
            'Mage_Core_Model_Resource_Layout_Update_Collection',
            array('addNoLinksFilter', 'addUpdatedDaysBeforeFilter', 'load'),
            array(),
            '',
            false
        );
        $layoutCollection->expects($this->once())
            ->method('addNoLinksFilter')
            ->will($this->returnSelf());
        $layoutCollection->expects($this->once())
            ->method('addUpdatedDaysBeforeFilter')
            ->with(self::TEST_DAYS_TO_EXPIRE)
            ->will($this->returnSelf());
        for ($i = 0; $i < 3; $i++) {
            $layout = $this->getMock('Mage_Core_Model_Layout_Update', array('delete'), array(), '', false);
            $layout->expects($this->once())
                ->method('delete');
            $layoutCollection->addItem($layout);
        }

        $objectManager = $this->getMock('Magento_ObjectManager_Zend', array('create'), array(), '', false);
        $objectManager->expects($this->at(0))
            ->method('create')
            ->with('Mage_Core_Model_Resource_Layout_Link_Collection')
            ->will($this->returnValue($linkCollection));
        $objectManager->expects($this->at(1))
            ->method('create')
            ->with('Mage_Core_Model_Resource_Layout_Update_Collection')
            ->will($this->returnValue($layoutCollection));

        // not important mocks
        /** @var $session Mage_Backend_Model_Session */
        $session = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false);
        $design  = $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false);

        // test
        $this->_model = new Mage_DesignEditor_Model_Observer($objectManager, $session, $design, $helper);
        $this->_model->clearLayoutUpdates();
    }
}
