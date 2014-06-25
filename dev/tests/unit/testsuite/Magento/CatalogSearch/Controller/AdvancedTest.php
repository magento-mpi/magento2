<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CatalogSearch\Controller;

class AdvancedTest extends \PHPUnit_Framework_TestCase
{
    public function testResultActionFiltersSetBeforeLoadLayout()
    {
        $filters = null;
        $expectedQuery = 'filtersData';

        $view = $this->getMock('Magento\Framework\App\View', array('loadLayout', 'renderLayout'), array(), '', false);
        $view->expects($this->once())->method('loadLayout')->will(
            $this->returnCallback(
                function () use (&$filters, $expectedQuery) {
                    $this->assertEquals($expectedQuery, $filters);
                }
            )
        );

        $request = $this->getMock('Magento\Framework\App\Console\Request', array('getQuery'), array(), '', false);
        $request->expects($this->once())->method('getQuery')->will($this->returnValue($expectedQuery));

        $catalogSearchAdvanced = $this->getMock(
            'Magento\CatalogSearch\Model\Advanced',
            array('addFilters', '__wakeup'),
            array(),
            '',
            false
        );
        $catalogSearchAdvanced->expects($this->once())->method('addFilters')->will(
            $this->returnCallback(
                function ($added) use (&$filters) {
                    $filters = $added;
                }
            )
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $context = $objectManager->getObject(
            'Magento\Framework\App\Action\Context',
            array('view' => $view, 'request' => $request)
        );

        /** @var \Magento\CatalogSearch\Controller\Advanced $instance */
        $instance = $objectManager->getObject(
            'Magento\CatalogSearch\Controller\Advanced',
            array('context' => $context, 'catalogSearchAdvanced' => $catalogSearchAdvanced)
        );
        $instance->resultAction();
    }
}
