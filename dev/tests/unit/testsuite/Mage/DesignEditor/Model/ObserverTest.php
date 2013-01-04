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
        // mocks
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

        $cacheManager = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);

        // test
        $this->_model = new Mage_DesignEditor_Model_Observer($objectManager, $helper, $cacheManager);
        $this->_model->clearLayoutUpdates();
    }

    public function testClearCache()
    {
        $objectManager = $this->getMock('Magento_ObjectManager_Zend', array(), array(), '', false);
        $helper = $this->getMock('Mage_DesignEditor_Helper_Data', array(), array(), '', false);

        $cacheManager = $this->getMock('Mage_Core_Model_Cache', array('flush'), array(), '', false);
        $cacheManager->expects($this->once())
            ->method('flush');

        $this->_model = new Mage_DesignEditor_Model_Observer($objectManager, $helper, $cacheManager);
        $this->_model->clearCache();
    }
}
