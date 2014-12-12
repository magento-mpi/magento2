<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerBalance\Model\Adminhtml;

/**
 * Test \Magento\CustomerBalance\Model\Adminhtml\Balance
 */
class BalanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Balance
     */
    protected $_model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Balance $model */
        $this->_model = $helper->getObject('Magento\CustomerBalance\Model\Adminhtml\Balance');
    }

    public function testGetWebsiteIdWithException()
    {
        $this->setExpectedException('Magento\Framework\Model\Exception', __('A website ID must be set.'));
        $this->_model->getWebsiteId();
    }

    public function testGetWebsiteId()
    {
        $this->_model->setWebsiteId('some id');
        $this->assertEquals('some id', $this->_model->getWebsiteId());
    }
}
