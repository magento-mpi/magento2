<?php
/**
 * Test Mage Api Session model
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Api Team <api-team@magento.com>
 * @todo Move to Unit tests
 */
class Mage_Oauth_Model_Consumer_Validator_KeyLengthTest extends Magento_TestCase
{
    /**
     * Test key length
     */
    const KEY_LENGTH = 32;

    /**
     * Key with right length
     */
    const KEY = 'd41d8cd98f00b204e9800998ecf8427e';

    /**
     * Failure validation data provider
     *
     * @return array
     */
    public function validationFailureProvider()
    {
        return array(
            array(self::KEY . 'z'),
            array(substr(self::KEY, 0, self::KEY - 1)),
        );
    }

    /**
     * Test validation key failure
     *
     * @param string $url
     * @dataProvider validationFailureProvider
     * @return void
     */
    public function testValidationFailure($url)
    {
        $validator = new Mage_Oauth_Model_Consumer_Validator_KeyLength(self::KEY_LENGTH);
        $this->assertFalse(
            $validator->isValid($url),
            'Expected failing validation.');
    }

    /**
     * Test validation key success
     *
     * @return void
     */
    public function testValidationSuccess()
    {
        $validator = new Mage_Oauth_Model_Consumer_Validator_KeyLength(self::KEY_LENGTH);
        $this->assertTrue(
            $validator->isValid(self::KEY),
            'Expected success validation but got following errors:' . PHP_EOL .
                implode(PHP_EOL, $validator->getMessages()));
    }

    /**
     * Test invalid key
     *
     * @return void
     */
    public function testInvalidValue()
    {
        $validator = new Mage_Oauth_Model_Consumer_Validator_KeyLength(self::KEY_LENGTH);
        $messages = $validator->getMessageTemplates();
        $message = $messages[Mage_Oauth_Model_Consumer_Validator_KeyLength::INVALID];
        try {
            $validator->isValid(array(self::KEY));

            //fail, no any exceptions
            $this->fail('Expected exception not catch.');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //skip PHPUnit exception
            throw $e;
        } catch (Exception $e) {
            $this->assertEquals('Exception', get_class($e),
                'Exception should be throw with "Exception" class.');
            $this->assertEquals($message, $e->getMessage(),
                sprintf('Expected exception message "%s".', $message));
        }
    }

}
