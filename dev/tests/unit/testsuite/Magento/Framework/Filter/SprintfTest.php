<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Filter;

class SprintTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $sprintfFilter = new \Magento\Framework\Filter\Sprintf('Formatted value: "%s"', 2, ',', ' ');
        $this->assertEquals('Formatted value: "1 234,57"', $sprintfFilter->filter(1234.56789));
    }
}
