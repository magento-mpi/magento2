<?php
/**
 * Magento\Customer\Service\Entity\AbstractDto
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

class AbstractDtoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage __construct
     */
    public function testNotCallingParentConstructor()
    {
        $badDto = new BadDtoExample();

        return clone $badDto;
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage __construct
     */
    public function testDataIsArray()
    {
        $badDto = new BadDtoExample();
        $badDto->setData([]);

        return clone $badDto;
    }
}
