<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

require_once __DIR__ . '/../../_files/Child.php';

class Magento_ObjectManager_Relations_RuntimeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager\Relations\Runtime
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\ObjectManager\Relations\Runtime();
    }

    /**
     * @param $type
     * @param $parents
     * @dataProvider getParentsDataProvider
     */
    public function testGetParents($type, $parents)
    {
        $this->assertEquals($parents, $this->_model->getParents($type));
    }

    public function getParentsDataProvider()
    {
        return array(
            array('Magento_Test_Di_Interface', array()),
            array('Magento_Test_Di_Parent', array(null, 'Magento_Test_Di_Interface')),
            array('Magento_Test_Di_Child', array('Magento_Test_Di_Parent', 'Magento_Test_Di_ChildInterface')),
        );
    }
}
