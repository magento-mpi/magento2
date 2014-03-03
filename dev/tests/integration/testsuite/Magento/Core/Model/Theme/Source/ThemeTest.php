<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Theme\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Theme Test
 *
 * @package Magento\View
 */
class ThemeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllOptions()
    {
        /** @var $model \Magento\Core\Model\Theme\Source\Theme */
        $model = Bootstrap::getObjectManager()->create('Magento\Core\Model\Theme\Source\Theme');

        /** @var $expectedCollection \Magento\Core\Model\Theme\Collection */
        $expectedCollection = Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Resource\Theme\Collection');
        $expectedCollection->addFilter('area', 'frontend');

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $model->getAllOptions(false);
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $model->getAllOptions(true);
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
