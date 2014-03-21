<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Source\Urlrewrite\OptionsTest.
 */
namespace Magento\Core\Model\Source\Urlrewrite;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Core\Model\Source\Urlrewrite\Options::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new \Magento\Core\Model\Source\Urlrewrite\Options();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array('' => 'No', 'R' => 'Temporary (302)', 'RP' => 'Permanent (301)');
        $this->assertEquals($expectedOptions, $options);
    }
}
