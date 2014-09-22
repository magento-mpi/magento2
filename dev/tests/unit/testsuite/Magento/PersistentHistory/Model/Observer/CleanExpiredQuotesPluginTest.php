<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

class CleanExpiredQuotesPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testBeforeExecute()
    {
        $plugin = new CleanExpiredQuotesPlugin();
        $subjectMock = $this->getMock(
            'Magento\Sales\Model\Observer\CleanExpiredQuotes',
            ['setExpireQuotesAdditionalFilterFields'],
            [],
            '',
            false);

        $subjectMock->expects($this->once())
            ->method('setExpireQuotesAdditionalFilterFields')
            ->with(['is_persistent' => 0])
            ->willReturn(null);

        $this->assertNull($plugin->beforeExecute($subjectMock));
    }
}
