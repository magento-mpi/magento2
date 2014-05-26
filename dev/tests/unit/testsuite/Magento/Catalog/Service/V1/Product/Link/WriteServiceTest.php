<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WriteService
     */
    protected $service;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->service = $helper->getObject('Magento\Catalog\Service\V1\Product\Link\WriteService');
    }

    public function testAssign()
    {
        $this->markTestIncomplete('need to be implemented');
    }
}
