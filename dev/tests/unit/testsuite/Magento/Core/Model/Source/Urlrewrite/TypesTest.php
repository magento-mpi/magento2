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
 * Test class for \Magento\Core\Model\Source\Urlrewrite\Types.
 */
class Magento_Core_Model_Source_Urlrewrite_TypesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Core\Model\Source\Urlrewrite\Types::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new \Magento\Core\Model\Source\Urlrewrite\Types();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array(
            1 => 'System',
            0 => 'Custom'
        );
        $this->assertEquals($expectedOptions, $options);
    }
}
