<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dkvashnin
 * Date: 12/17/13
 * Time: 1:10 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Usa\Helper;


class DataTest extends \PHPUnit_Framework_TestCase
{
    public function _getHelperDataInstance()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = array(
            'context' => $this->getMock('Magento\App\Helper\Context', array(), array(), '', false),
            'locale' => $this->getMock('Magento\Core\Model\Locale', array(), array(), '', false)
        );

        return $helper->getObject('Magento\Usa\Helper\Data', $arguments);
    }

    /**
     * @dataProvider shippingMethodProvider
     */
    public function testDisplayGirthValue($shippingMethod)
    {
        $helperData = $this->_getHelperDataInstance();
        $this->assertTrue($helperData->displayGirthValue($shippingMethod));
    }

    public function shippingMethodProvider()
    {
        return array(
            array('usps_0_FCLE'), // First-Class Mail Large Envelope
            array('usps_1'),      // Priority Mail
            array('usps_2'),      // Priority Mail Express Hold For Pickup
            array('usps_3'),      // Priority Mail Express
            array('usps_4'),      // Standard Post
            array('usps_6'),      // Media Mail
            array('usps_INT_1'),  // Priority Mail Express International
            array('usps_INT_2'),  // Priority Mail International
            array('usps_INT_4'),  // Global Express Guaranteed (GXG)
            array('usps_INT_7'),  // Global Express Guaranteed Non-Document Non-Rectangular
            array('usps_INT_8'),  // Priority Mail International Flat Rate Envelope
            array('usps_INT_9'),  // Priority Mail International Medium Flat Rate Box
            array('usps_INT_10'), // Priority Mail Express International Flat Rate Envelope
            array('usps_INT_11'), // Priority Mail International Large Flat Rate Box
            array('usps_INT_12'), // USPS GXG Envelopes
            array('usps_INT_14'), // First-Class Mail International Large Envelope
            array('usps_INT_16'), // Priority Mail International Small Flat Rate Box
            array('usps_INT_20'), // Priority Mail International Small Flat Rate Envelope
            array('usps_INT_26'), // Priority Mail Express International Flat Rate Boxes
        );
    }
}
