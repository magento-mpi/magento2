<?php
/**
 * Test Url validator model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_Core_Model_Url_ValidatorTest extends Mage_PHPUnit_TestCase
{
    /**
     * Validation data provider
     *
     * @return array
     */
    public function validationFailureProvider()
    {
        return array(
            array('invalid://domain?params'),
            array('some://domain.some?params'),
            array('http://example.com?param^=1'),
            array('http://example.com/some space/1/?param=1'),
        );
    }

    /**
     * Test validation URL failure
     *
     * @param string $url
     * @dataProvider validationFailureProvider
     * @return void
     */
    public function testValidationFailure($url)
    {
        $validator = new Mage_Core_Model_Url_Validator();
        $this->assertFalse(
            $validator->isValid($url),
            'Expected fail validation.');
    }

    /**
     * Validation data provider
     *
     * @return array
     */
    public function validationSuccessProvider()
    {
        return array(
            array('http://example.com/model/?param1=param11&?param2=param22'),
            array('http://example.com/model/param/param1'),
            array('http://example.com/some%20space/1/?param=1'),
            array('http://example.com/some+space/1/?param=1'),
        );
    }

    /**
     * Test validation URL success
     *
     * @param string $url
     * @dataProvider validationSuccessProvider
     * @return void
     */
    public function testValidationSuccess($url)
    {
        $validator = new Mage_Core_Model_Url_Validator();
        $this->assertTrue(
            $validator->isValid($url),
            'Expected success validation but got following errors:' . PHP_EOL .
                implode(PHP_EOL, $validator->getMessages()));
    }

}
