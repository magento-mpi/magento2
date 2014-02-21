<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto\Eav;

use Magento\Customer\Service\V1\Dto\Eav\Option;
use Magento\Customer\Service\V1\Dto\Eav\OptionBuilder;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    const LABEL = 'LABEL';

    const VALUE = 'VALUE';

    public function testConstructorAndGetters()
    {
        $optionBuilder = (new OptionBuilder())->setLabel(self::LABEL)->setValue(self::VALUE);
        $option = new Option($optionBuilder);
        $this->assertSame(self::LABEL, $option->getLabel());
        $this->assertSame(self::VALUE, $option->getValue());
    }
}
