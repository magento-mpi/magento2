<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model\Resource;

class IteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Model\Resource\Iterator
     */
    protected $_model;

    /**
     * Counter for testing walk() callback
     *
     * @var int
     */
    protected $_callbackCounter = 0;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Model\Resource\Iterator');
    }

    /**
     * Helper callback for testWalk()
     *
     * @param array $data
     * @return bool
     */
    public function walkCallback($data)
    {
        $this->_callbackCounter = $data['idx'];
        return true;
    }

    /**
     * @expectedException \Magento\Model\Exception
     */
    public function testWalkException()
    {
        $this->_model->walk('test', array(array($this, 'walkCallback')));
    }
}
