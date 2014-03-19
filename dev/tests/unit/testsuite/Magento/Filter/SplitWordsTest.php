<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filter;

class SplitWordsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Bug: $maxWordLength parameter has a misleading name. It limits qty of words in the result.
     */
    public function testSplitWords()
    {
        $words = '123  123  45 789';
        $filter = new \Magento\Filter\SplitWords(false, 3);
        $this->assertEquals(array('123', '123', '45'), $filter->filter($words));
        $filter = new \Magento\Filter\SplitWords(true, 2);
        $this->assertEquals(array('123', '45'), $filter->filter($words));
    }
}
