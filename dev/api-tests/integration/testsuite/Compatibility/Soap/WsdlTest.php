<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  compatibility_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Wsdl compatibility between previous and current API versions.
 */
class Compatibility_Soap_WsdlTest extends Magento_Test_Webservice_Compatibility
{

    /**
     * Product created at previous API
     * @var SimpleXMLElement
     */
    protected static $_prevWsdl;

    /**
     * Product created at current API
     * @var SimpleXMLElement
     */
    protected static $_currWsdl;

    /**
     * Set up values for previous and current wsdl.
     *
     * Scenario:
     * 1. Load wsdl on previous API.
     * 2. Load wsdl on current API.
     * 3. Set Namespase. Note: simplexml will work incorrect if Namespase did not set.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_prevWsdl = simplexml_load_file(TESTS_PREV_WEBSERVICE_URL . "/api/v2_soap/?wsdl");
        self::$_currWsdl = simplexml_load_file(TESTS_WEBSERVICE_URL . "/api/v2_soap/?wsdl");
        self::$_currWsdl->registerXPathNamespace("n", "http://schemas.xmlsoap.org/wsdl/");
        self::$_prevWsdl->registerXPathNamespace("n", "http://schemas.xmlsoap.org/wsdl/");
    }

    /**
     * Test wsdl compatibility.
     *
     * Scenario:
     * 1. Load wsdl on previous API.
     * 2. Load wsdl on current API.
     * 3. Load array of all operations on current API.
     * 4. Compare operation's name on current and previous API.
     * 5. Compare operation's input on current and previous API.
     * 6. Compare operation's input message on current and previous API.
     * 7. Compare operation's output on current and previous API.
     * 8. Compare operation's output message on current and previous API.
     * Expected result:
     * Current wsdl is the same as in previous API except new operation in current API.
     *
     */
   public  function testWsdlCompatibility()
   {
       foreach(self::$_prevWsdl->xpath('//n:portType/n:operation') as $operation){

           // Finds and compares operation name
           $foundOperation = self::$_currWsdl->xpath('//n:portType/n:operation[@name="' . $operation['name'] . '"]');
           $this->assertNotEmpty($foundOperation, ' Operation ' . $operation['name'] . ' was not found in current wsdl.');

           // Finds and compares input data
           $foundInputElement = self::$_currWsdl->xpath('//n:portType/n:operation/n:input[@message="' .
               (string)$operation->input['message'] . '"]');
           $this->assertNotEmpty($foundInputElement, 'Input element ' . (string)$operation->input['message'] .
               ' in operation '. $operation['name'] . ' was not found in current wsdl.');
           $this->compareMessagePart(str_replace('typens:', '', (string)$operation->input['message']));

            // Finds and compares output data
           $foundOutputElement = self::$_currWsdl->xpath('//n:portType/n:operation/n:output[@message="' .
               (string)$operation->output['message'] . '"]');
           $this->assertNotEmpty($foundOutputElement, 'Output element ' . (string)$operation->output['message'] .
               ' in operation '. $operation['name'] . ' was not found in current wsdl.');
           $this->compareMessagePart(str_replace('typens:', '', (string)$operation->output['message']));
       }
   }

    public  function compareComplexType($type)
    {

    }

    /** Check message part of operation. Works with input and output message
     *
     * @param $messageName
     */
    public  function compareMessagePart($messageName)
    {
        // Load operation messages
        $foundCurrMessage = self::$_currWsdl->xpath('//n:message[@name="' . $messageName . '"]');
        $foundPrevMessage = self::$_prevWsdl->xpath('//n:message[@name="' . $messageName . '"]');
        $this->assertNotEmpty($foundCurrMessage,'Message ' . $messageName . 'was not found in current wsdl.');
        $this->assertNotEmpty($foundPrevMessage,'Message ' . $messageName . 'was not found in previous wsdl.');

        // Check parts 'name' and 'type'
        foreach($foundPrevMessage[0]->part as $messagePart){
            $foundCurrPart = self::$_currWsdl->xpath('//n:message[@name="' . $messageName . '"]/n:part[@name="' .
                $messagePart['name'] . '"]');

            // Check that part 'name' and 'type' are equals
            $this->assertEquals((string)$foundCurrPart[0]['name'],(string)$messagePart['name'],'Message part ' .
                $messagePart['name'] . ' was not found in operation message ' . $messageName);
            $this->assertEquals((string)$foundCurrPart[0]['type'],(string)$messagePart['type'],'Message type ' .
                $messagePart['type'] . ' was not found in operation message ' . $messageName);
            $messageType = explode(':', $messagePart['type']);
            if ($messageType[0] == 'typens'){
                $this->compareComplexType($messageType[1]);
            }
        }

    }

}