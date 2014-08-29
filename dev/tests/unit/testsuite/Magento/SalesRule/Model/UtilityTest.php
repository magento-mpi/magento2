<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model;

/**
 * Class UtilityTest
 */
class UtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $model;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject('Magento\SalesRule\Model\Utility');
    }

    public function testResetRoundingDeltas()
    {
        $this->assertNull($this->model->resetRoundingDeltas());
    }
}
