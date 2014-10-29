<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

use Magento\Customer\Model\Data\Option;
use Magento\Customer\Api\Data\OptionDataBuilder;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    const LABEL = 'LABEL';

    const VALUE = 'VALUE';

    public function testConstructorAndGetters()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $optionBuilder = $helper->getObject('\Magento\Customer\Api\Data\OptionDataBuilder')
            ->setLabel(self::LABEL)->setValue(self::VALUE);
        $option = new Option($optionBuilder);
        $this->assertSame(self::LABEL, $option->getLabel());
        $this->assertSame(self::VALUE, $option->getValue());
    }
}
