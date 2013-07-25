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
 * Test theme label
 */
class Mage_Core_Model_Theme_LabelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collection;

    /**
     * @var Mage_Core_Model_Theme_Label
     */
    protected $_model;

    protected function setUp()
    {
        $this->_helper = $this->getMock('Mage_Core_Helper_Data', array('__'), array(), '', false);
        $this->_helper->expects($this->any())->method('__')->will($this->returnCallback(function () {
            $arguments = func_get_args();
            return call_user_func_array('sprintf', $arguments);
        }));

        $this->_collection = $this->getMock('Mage_Core_Model_Resource_Theme_Collection', array(), array(), '', false);
        $collectionFactory = $this->getMock('Mage_Core_Model_Resource_Theme_CollectionFactory',
            array('create'), array(), '', false);
        $collectionFactory->expects($this->any())->method('create')->will($this->returnValue($this->_collection));

        $this->_model = new Mage_Core_Model_Theme_Label($collectionFactory, $this->_helper);
    }
}
