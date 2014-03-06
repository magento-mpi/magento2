<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\GiftRegistry\Block\Form\Element
 */
namespace Magento\GiftRegistry\Block\Form;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCalendarDateHtml()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\GiftRegistry\Block\Form\Element');

        $value = null;
        $formatType = \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_FULL;

        $html = $block->getCalendarDateHtml('date_name', 'date_id', $value, $formatType);

        $dateFormat = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Stdlib\DateTime\TimezoneInterface')->getDateFormat($formatType);

        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('value=""', $html);
    }
}
