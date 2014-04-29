<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\ObjectManager\Relations;



require_once __DIR__ . '/../../../_files/Child.php';
class RuntimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager\Relations\Runtime
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Framework\ObjectManager\Relations\Runtime();
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
            array('Magento\Test\Di\DiInterface', array()),
            array('Magento\Test\Di\DiParent', array(null, 'Magento\Test\Di\DiInterface')),
            array('Magento\Test\Di\Child', array('Magento\Test\Di\DiParent', 'Magento\Test\Di\ChildInterface'))
        );
    }
}
