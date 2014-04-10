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
 * Test class for \Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit
 */
namespace Magento\GiftRegistry\Block\Customer\Edit;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Stub class name
     */
    const STUB_CLASS = 'Magento_GiftRegistry_Block_Customer_Edit_AbstractEdit_Stub';

    public function testGetCalendarDateHtml()
    {
        $this->getMockForAbstractClass(
            'Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit',
            array(),
            self::STUB_CLASS,
            false
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\State')->setAreaCode('frontend');
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            self::STUB_CLASS
        );

        $value = null;
        $formatType = \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM;

        $html = $block->getCalendarDateHtml('date_name', 'date_id', $value, $formatType);

        $dateFormat = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Stdlib\DateTime\TimezoneInterface'
        )->getDateFormat(
            $formatType
        );
        $value = $block->formatDate($value, $formatType);

        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('value="' . $value . '"', $html);
    }
}
