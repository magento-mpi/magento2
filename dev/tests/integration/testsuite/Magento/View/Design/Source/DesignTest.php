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

namespace Magento\View\Design\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Design Test
 *
 * @package Magento\View
 */
class DesignTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllOptions()
    {
        /** @var $model \Magento\View\Design\Source\Design */
        $model = Bootstrap::getObjectManager()
            ->create('Magento\View\Design\Source\Design');

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
