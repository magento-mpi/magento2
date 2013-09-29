<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ImportExport\Block\Adminhtml\Export\Filter
 */
namespace Magento\ImportExport\Block\Adminhtml\Export;

class FilterTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateFromToHtmlWithValue()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\ImportExport\Block\Adminhtml\Export\Filter');
        $method = new \ReflectionMethod(
                    'Magento\ImportExport\Block\Adminhtml\Export\Filter', '_getDateFromToHtmlWithValue');
        $method->setAccessible(true);

        $arguments = array(
            'data' => array(
                'attribute_code' => 'date',
                'backend_type'   => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            )
        );
        $attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Eav\Model\Entity\Attribute', $arguments);
        $html = $method->invoke($block, $attribute, null);
        $this->assertNotEmpty($html);

        $dateFormat = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\LocaleInterface')
            ->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $pieces = array_filter(explode('<strong>', $html));
        foreach ($pieces as $piece) {
            $this->assertContains('dateFormat: "' . $dateFormat . '",', $piece);
        }
    }
}
