<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    public function setUp()
    {
        $this->scopeConfig = $this->getMock(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            [],
            [],
            '',
            false,
            false
        );
        $this->config = new Config($this->scopeConfig);
    }

    public function testIsArchiveActive()
    {
        $isActive = false;
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(
                $this->equalTo(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_ACTIVE),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue($isActive));
        $this->assertEquals($isActive, $this->config->isArchiveActive());
    }

    public function testGetArchiveAge()
    {
        $age = 12;

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                $this->equalTo(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_AGE),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue($age));
        $this->assertEquals($age, $this->config->getArchiveAge());
    }

    public function testGetArchiveOrderStatuses()
    {
        $statuses = 'archived,closed';

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                $this->equalTo(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_ORDER_STATUSES),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue($statuses));
        $statuses = explode(',', $statuses);
        $this->assertEquals($statuses, $this->config->getArchiveOrderStatuses());
    }

    public function testGetArchiveOrderStatusesEmpty()
    {
        $empty = [];
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                $this->equalTo(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_ORDER_STATUSES),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue($empty));
        $this->assertEquals($empty, $this->config->getArchiveOrderStatuses());
    }
}
