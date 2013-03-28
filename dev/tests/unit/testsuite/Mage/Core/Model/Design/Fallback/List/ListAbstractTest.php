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

class Mage_Core_Model_Design_Fallback_List_ListAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Fallback_List_ListAbstract
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    public function setUp()
    {
        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_model = $this->getMockForAbstractClass('Mage_Core_Model_Design_Fallback_List_ListAbstract',
            array($this->_dirs));
    }

    public function testConstructor()
    {
        $this->_model->expects($this->once())->method('_getFallbackRules');
        $this->_model->__construct($this->_dirs);
    }

    public function testGetPatternDirs()
    {
        $ruleOne = $this->getMock('Mage_Core_Model_Design_Fallback_Rule_Simple', array('getPatternDirs'), array(), '',
            false);
        $ruleOne->expects($this->once())
            ->method('getPatternDirs')
            ->will($this->returnValue(array(1)));

        $ruleTwo = $this->getMock('Mage_Core_Model_Design_Fallback_Rule_Simple', array('getPatternDirs'), array(), '',
            false);
        $ruleTwo->expects($this->once())
            ->method('getPatternDirs')
            ->will($this->returnValue(array(2)));

        $rules = new ReflectionProperty($this->_model, '_rules');
        $rules->setAccessible(true);
        $rules->setValue($this->_model, array($ruleOne, $ruleTwo));

        $this->assertSame(array(1,2), $this->_model->getPatternDirs(array()));
    }
}
