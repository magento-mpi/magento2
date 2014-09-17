<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Rss;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class DiscountsTest
 * @package Magento\SalesRule\Model\Rss
 */
class DiscountsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Rss\Discounts
     */
    protected $discounts;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTime;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;

    protected function setUp()
    {
        $this->dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime');
        $this->collectionFactory = $this->getMock('Magento\SalesRule\Model\Resource\Rule\CollectionFactory');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->discounts = $this->objectManagerHelper->getObject(
            'Magento\SalesRule\Model\Rss\Discounts',
            [
                'dateTime' => $this->dateTime,
                'collectionFactory' => $this->collectionFactory
            ]
        );
    }

    public function testGetDiscountCollection()
    {
    }
}
