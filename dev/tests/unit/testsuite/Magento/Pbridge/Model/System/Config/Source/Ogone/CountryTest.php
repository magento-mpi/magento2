<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Model\System\Config\Source\Ogone;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CountryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pbridge\Model\System\Config\Source\Ogone\Country */
    protected $country;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->country = $this->objectManagerHelper->getObject(
            'Magento\Pbridge\Model\System\Config\Source\Ogone\Country'
        );
    }

    public function testToOptionArray()
    {
        $expected = [
            ['value' => 'AT', 'label' => __('Austria')],
            ['value' => 'DE', 'label' => __('Germany')],
            ['value' => 'NL', 'label' => __('Netherlands')],
        ];
        $this->assertEquals($expected, $this->country->toOptionArray());
    }
}
