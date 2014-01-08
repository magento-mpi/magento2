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
        $this->_model->setExcludedIds(array('fixture_code_2'));
        $result = $this->_model->shift();
        // Only free non-excluded code should be selected
        $this->assertSame('fixture_code_3', $result);
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
