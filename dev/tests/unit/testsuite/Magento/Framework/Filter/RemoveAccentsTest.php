<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Filter;

class RemoveAccentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $string
     * @param bool $german
     * @param string $expected
     *
     * @dataProvider removeAccentsDataProvider
     */
    public function testRemoveAccents($string, $german, $expected)
    {
        $filter = new \Magento\Framework\Filter\RemoveAccents($german);
        $this->assertEquals($expected, $filter->filter($string));
    }

    /**
     * @return array
     */
    public function removeAccentsDataProvider()
    {
        return [
            'general conversion' => ['ABCDEFGHIJKLMNOPQRSTUVWXYZ', false, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'],
            'conversion with german specifics' => ['äöüÄÖÜß', true, 'aeoeueAeOeUess']
        ];
    }
}
