<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Model\Wrapping;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Model\Wrapping\Validator */
    protected $validator;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->validator = $this->objectManagerHelper->getObject('Magento\GiftWrapping\Model\Wrapping\Validator');
    }

    public function testValidateWithError()
    {
        $presentFields = [
            'status' => 'Status',
            'base_price' => 'Price'
        ];
        $wrapping = $this->objectManagerHelper->getObject('Magento\GiftWrapping\Model\Wrapping');
        // prepare data
        foreach ($presentFields as $key => $val) {
            $wrapping->setData($key, $val);
        }

        $this->assertFalse($this->validator->isValid($wrapping));
    }

    public function testValidateSuccess()
    {
        $presentFields = [
            'status' => 'Status',
            'base_price' => 'Price',
            'design' => __('Field is required: Gift Wrapping Design')
        ];

        $wrapping = $this->objectManagerHelper->getObject('Magento\GiftWrapping\Model\Wrapping');
        // prepare data
        foreach ($presentFields as $key => $val) {
            $wrapping->setData($key, $val);
        }

        $this->assertTrue($this->validator->isValid($wrapping));
    }
}
