<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab;

class AlertsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts
     */
    protected $alerts;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeConfigMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);

        $this->alerts = $helper->getObject('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts', array(
                'storeConfig' => $this->storeConfigMock
            )
        );
    }

    /**
     * @param bool $priceAllow
     * @param bool $stockAllow
     * @param bool $canShowTab
     *
     * @dataProvider canShowTabDataProvider
     */
    public function testCanShowTab($priceAllow, $stockAllow, $canShowTab)
    {
        $valueMap = array(
            array('catalog/productalert/allow_price', null, $priceAllow),
            array('catalog/productalert/allow_stock', null, $stockAllow)
        );
        $this->storeConfigMock->expects($this->any())->method('getConfig')->will($this->returnValueMap($valueMap));
        $this->assertEquals($canShowTab, $this->alerts->canShowTab());
    }

    public function canShowTabDataProvider()
    {
        return array(
            'alert_price_and_stock_allow' => array(
                true, true, true
            ),
            'alert_price_is_allowed_and_stock_is_unallowed' => array(
                true, false, true
            ),
            'alert_price_is_unallowed_and_stock_is_allowed' => array(
                false, true, true
            ),
            'alert_price_is_unallowed_and_stock_is_unallowed' => array(
                false, false, false
            )
        );
    }

}

