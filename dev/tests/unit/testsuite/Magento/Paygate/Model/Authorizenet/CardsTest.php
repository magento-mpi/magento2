<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorizenet\Model\Authorizenet;

class CardsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Authorizenet\Model\Authorizenet\Card
     */
    protected $_object;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_object = $objectManagerHelper->getObject('Magento\Authorizenet\Model\Authorizenet\Cards');
    }

    /**
     * @dataProvider setPaymentDataProvider
     * @param string $cardId
     * @param array $cardsData
     * @param array $additionalInfo
     * @param \Magento\Object $expectedResult
     */
    public function testSetPayment($cardId, $cardsData, $additionalInfo, $expectedResult)
    {
        $paymentsMock = $this->getMockBuilder('\Magento\Payment\Model\Info')
            ->disableOriginalConstructor()
            ->getMock();

        $paymentsMock->expects($this->at(0))
            ->method('getAdditionalInformation')
            ->with('authorize_cards')
            ->will($this->returnValue(array($cardId => $cardsData)));

        $paymentsMock->expects($this->at(1))
            ->method('getAdditionalInformation')
            ->will($this->returnValue($additionalInfo));

        $this->_object->setPayment($paymentsMock);

        $this->assertEquals(
            $this->_object->getCard($cardId),
            $expectedResult
        );
    }

    /**
     * @return array
     */
    public function setPaymentDataProvider()
    {
        return array(
            array(
                'cardId', array('key' => 'value'), array('key' => 'value'),
                new \Magento\Object(
                    array(
                        'key' => 'value',
                        'additional_information' => array('key' => 'value')
                    )
                ),
            ),
            array(
                'cardId', array('key' => 'value'), array('key2' => 'value2'),
                new \Magento\Object(
                    array(
                        'key' => 'value',
                        'additional_information' => array('key2' => 'value2')
                    )
                ),
            ),
            array(
                'cardId', array('key' => 'value'), array(),
                new \Magento\Object(
                    array(
                        'key' => 'value',
                        'additional_information' => array()
                    )
                ),
            )
        );
    }
}
