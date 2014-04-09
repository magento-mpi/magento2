<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The requested asset type was 'ext', but ended up with 'ext2'
     */
    public function testGetFileBadContentType()
    {
        $this->markTestIncomplete('MAGETWO-21654');
        $asset = $this->getMockForAbstractClass('\Magento\View\Asset\LocalInterface');
        $chain = new Chain($asset, '', 'ext');
        $chain->setContentType('ext2');
        $chain->assertValid();
    }
} 
