<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Model\System\Config\Source\Worldpay;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class AccountTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pbridge\Model\System\Config\Source\Worldpay\AccountType */
    protected $accountType;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->accountType = $this->objectManagerHelper->getObject(
            'Magento\Pbridge\Model\System\Config\Source\Worldpay\AccountType'
        );
    }

    public function testToOptionArray()
    {
        $expected = [
            ['value' => 'business', 'label' => __('Business')],
            ['value' => 'corporate', 'label' => __('Corporate')],
        ];
        $this->assertEquals($expected, $this->accountType->toOptionArray());
    }
}
