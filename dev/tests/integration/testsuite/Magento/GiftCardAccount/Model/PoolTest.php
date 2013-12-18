<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Model;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCardAccount\Model\Pool
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftCardAccount\Model\Pool');
    }

    /**
     * @magentoDataFixture Magento/GiftCardAccount/_files/codes_pool.php
     */
    public function testShift()
    {
        $result = $this->_model->shift();
        $this->assertNotEmpty($result);
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage No codes left in the pool
     */
    public function testShiftNoCodeLeft()
    {
        $this->_model->shift();
    }
}
