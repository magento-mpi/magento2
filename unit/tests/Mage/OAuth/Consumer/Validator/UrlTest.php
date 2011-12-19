<?php
/**
 * Test Mage Api Session model
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_OAuth_Model_Consumer_Validator_UrlTest extends Mage_PHPUnit_TestCase
{
    /**
     * Validation data provider
     *
     * @return array
     */
    public function validationFailureProvider()
    {
        return array(
            array('http://example.com?oauth_key=key'),
            array('http://example.com?key=key&oauth_secret=secret'),
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
        $validator = new Mage_OAuth_Model_Consumer_Validator_Url();
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
            array('http://example.com/oauth_model/?oauthKey=key'),
            array('http://example.com/oauth_model/?key=key&oauthSecret=secret'),
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
        $validator = new Mage_OAuth_Model_Consumer_Validator_Url();
        $this->assertTrue(
            $validator->isValid($url),
            'Expected success validation.');
    }

}
