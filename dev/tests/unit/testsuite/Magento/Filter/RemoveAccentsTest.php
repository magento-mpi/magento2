<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filter;

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
        $filter = new \Magento\Filter\RemoveAccents($german);
        $this->assertEquals($expected, $filter->filter($string));
    }

    /**
     * @return array
     */
    public function removeAccentsDataProvider()
    {
        return array(
            'general conversion' => array('ABCDEFGHIJKLMNOPQRSTUVWXYZ', false, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            'conversion with german specifics' => array('äöüÄÖÜß', true, 'aeoeueAeOeUess')
        );
    }
}
