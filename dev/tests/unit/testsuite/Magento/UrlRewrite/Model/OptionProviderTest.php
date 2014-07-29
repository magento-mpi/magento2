<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Model;

class OptionProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $model = new OptionProvider();
        $options = $model->toOptionArray();
        $this->assertInternalType('array', $options);
        $expectedOptions = array('' => 'No', 302 => 'Temporary (302)', 301 => 'Permanent (301)');
        $this->assertEquals($expectedOptions, $options);
    }
}
