<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto\Eav;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    const LABEL = 'LABEL';

    const VALUE = 'VALUE';

    public function testConstructorAndGetters()
    {
        $option = new \Magento\Customer\Service\V1\Dto\Eav\Option([
            'label' => self::LABEL,
            'value' => self::VALUE
        ]);

        $this->assertSame(self::LABEL, $option->getLabel());
        $this->assertSame(self::VALUE, $option->getValue());
    }
}
