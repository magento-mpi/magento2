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

        $this->_model = new Mage_DesignEditor_Model_Observer($session, $design);
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
}
