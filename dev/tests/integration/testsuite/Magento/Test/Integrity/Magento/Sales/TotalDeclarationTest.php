<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Sales;

class TotalDeclarationTest extends \PHPUnit_Framework_TestCase
{
    public function testTotalDeclarations()
    {
        $config = array();
        /** @var $configModel \Magento\Core\Model\Config */
        $configModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\Config');
        foreach ($configModel->getNode('global/sales/quote/totals')->asCanonicalArray() as $key => $row) {
            $config[$key] = array(
                'before' => empty($row['before']) ? array() : explode(',', $row['before']),
                'after'  => empty($row['after']) ? array() : explode(',', $row['after']),
            );
        }
        \Magento\Sales\Model\Config\Ordered::validateCollectorDeclarations($config);
    }
}
