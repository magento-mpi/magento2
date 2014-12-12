<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
