<?php
/**
 * \Magento\Webhook\Model\Source\Format
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Source;

class FormatTest extends \Magento\Webhook\Model\Source\Pkg
{
    public function testGetFormatsForForm()
    {
        $unitUnderTest = new \Magento\Webhook\Model\Source\Format(array('type' => 'blah'));
        $elements = $unitUnderTest->getFormatsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getFormatsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}
