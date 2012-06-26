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
     * Xpath constans
     */
    const OPERATIONS = '//n:portType/n:operation';
    const OPERATION_NAME ='//n:portType/n:operation[@name="%s"]';
    const OPERATION_INPUT = '//n:portType/n:operation/n:input[@message="%s"]';
    const OPERATION_OUTPUT = '//n:portType/n:operation/n:output[@message="%s"]';
    const MESSAGE = '//n:message[@name="%s"]';
    const MESSAGE_PART_NAME= '//n:message[@name="%s"]/n:part[@name="%s"]';
    const COMPLEX_CONTENT_ATTRIBUTE = '//c:complexType[@name="%s"]/c:complexContent/c:restriction/c:attribute';
    const ARRAY_TYPE_NAMESPACE = 'http://schemas.xmlsoap.org/wsdl/';

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
        // Register Namespace for 'messages' and 'operations'
        self::$_currWsdl->registerXPathNamespace("n", "http://schemas.xmlsoap.org/wsdl/");
        self::$_prevWsdl->registerXPathNamespace("n", "http://schemas.xmlsoap.org/wsdl/");
        // Register Namespace for 'complex type'
        self::$_currWsdl->registerXPathNamespace("c", "http://www.w3.org/2001/XMLSchema");
        self::$_prevWsdl->registerXPathNamespace("c", "http://www.w3.org/2001/XMLSchema");
    }

    /**
     * Test wsdl compatibility.
     *
     * Scenario:
     * 1. Load array of all 'operation' on current API.
     * 2. Compare operation's name on current and previous API.
     * 3. Compare operation's input on current and previous API.
     * 4. Compare operation's input message on current and previous API.
     * 5. Compare operation's output on current and previous API.
     * 6. Compare operation's output message on current and previous API.
     * Expected result:
     * Current wsdl is the same as in previous API except new operation in current API.
     *
     */
   public function testWsdlCompatibility()
   {
       // Load array of all 'operation' on current API
       foreach(self::$_prevWsdl->xpath(self::OPERATIONS) as $operation){
           // Finds and compares 'operation['name']'
           $foundOperation = self::$_currWsdl->xpath(sprintf(self::OPERATION_NAME, $operation['name']));
           $this->assertNotEmpty($foundOperation, ' Operation ' . $operation['name'] . ' was not found in current wsdl.');

           // Finds and compares 'input['message']' data
           $foundInputElement = self::$_currWsdl->xpath(sprintf(self::OPERATION_INPUT,
               (string)$operation->input['message']));
           $this->assertNotEmpty($foundInputElement, 'Input element ' . (string)$operation->input['message'] .
               ' in operation '. $operation['name'] . ' was not found in current wsdl.');
           $this->compareMessagePart(str_replace('typens:', '', (string)$operation->input['message']));

            // Finds and compares output data
           $foundOutputElement = self::$_currWsdl->xpath(sprintf(self::OPERATION_OUTPUT,
               (string)$operation->output['message']));
           $this->assertNotEmpty($foundOutputElement, 'Output element ' . (string)$operation->output['message'] .
               ' in operation '. $operation['name'] . ' was not found in current wsdl.');
           $this->compareMessagePart(str_replace('typens:', '', (string)$operation->output['message']));
       }
   }

    /**
     * Check message part of operation. Works with input and output message
     * @param $messageName
     */
    protected function compareMessagePart($messageName)
    {
        // Load operation messages
        $foundCurrMessage = self::$_currWsdl->xpath(sprintf(self::MESSAGE, $messageName));
        $foundPrevMessage = self::$_prevWsdl->xpath(sprintf(self::MESSAGE, $messageName));
        // Check that messages is loaded in bouth wsdl
        $this->assertNotEmpty($foundCurrMessage,'Message: ' . $messageName . ' was not found in current wsdl.');
        $this->assertNotEmpty($foundPrevMessage,'Message: ' . $messageName . ' was not found in previous wsdl.');

        // Check part['name'] and part['type']
        $prevMessage = reset($foundPrevMessage);
        if(property_exists($prevMessage, 'part')){
            foreach($prevMessage->part as $prevMessagePart){
                // Check if there is 'part' attribute in 'message' on current wsdl
                $currMessagePart = self::$_currWsdl->xpath(sprintf(self::MESSAGE_PART_NAME,
                    $messageName, $prevMessagePart['name']));
                $this->assertInternalType('array', $currMessagePart);
                $this->assertNotEmpty($currMessagePart, 'Message \'part\' ' . $prevMessagePart['name'] .
                    ' was not found in \'operation\' ' . $messageName . ' in current wsdl.');

                // Check that parts 'name' and 'type' are equal
                $this->assertEquals((string)$prevMessagePart['type'], (string)$currMessagePart[0]['type'],
                    'Message \'type\' ' . $prevMessagePart['type'] . ' was not found in \'operation message\' ' .
                    $messageName . ' in current wsdl.');

                $prevMessageType = explode(':', $prevMessagePart['type']);

                if ($prevMessageType[0] == 'typens' && $prevMessageType[1] != null){
                    $this->compareComplexType($prevMessageType[1]);
                }
            }
        }
    }

    /**
     * Comparing 'message' ComplexType 'type'
     * @param $typeName
     */
    protected function compareComplexType($typeName)
    {
        // Load 'type' by name = $typeName object
        $foundPrevComplexType = self::$_prevWsdl->xpath('//c:complexType[@name="' . $typeName . '"]');
        $foundCurrComplexType = self::$_currWsdl->xpath('//c:complexType[@name="' . $typeName . '"]');

        // Check that 'type' with name = $typeName presented in current wsdl
        $this->assertNotEmpty($foundCurrComplexType,
            '\'Complex Type\': ' . $typeName . ' was not found in current wsdl.');

        // Check 'type' description in case if in previous wsdl 'type' presented but not described
        $this->assertNotEmpty($foundPrevComplexType,
            '\'Complex Type\': ' . $typeName . ' was not found in previous wsdl.');

        // Compare ComplexType with ComplexContent
        if(key_exists('complexContent', reset($foundPrevComplexType))){
            // Load value for 'arrayType' in ComplexContent 'attribute' that is in spesific namespace
            $prevAttributeType = self::$_prevWsdl->xpath(sprintf(self::COMPLEX_CONTENT_ATTRIBUTE, $typeName));
            $prevType = reset($prevAttributeType)->attributes(self::ARRAY_TYPE_NAMESPACE);
            $currAttributeType = self::$_currWsdl->xpath(sprintf(self::COMPLEX_CONTENT_ATTRIBUTE, $typeName));
            $currType = reset($currAttributeType)->attributes(self::ARRAY_TYPE_NAMESPACE);

            // Compare found values for 'arrayType'
            $this->assertEquals((string)$prevType['arrayType'], (string)$currType['arrayType'],
                '\'Complex Type\': ' . $typeName . ' with \'attribute[\'arrayType\']\' = '
                . (string)$prevType['arrayType'] . ' was not found in current wsdl.');

            // If 'arrayType' == typens:typeName ->  compareComplexType($typeName)
            $prevType = explode(':', (string)$prevType['arrayType']);
            if ($prevType[0] == 'typens' && $prevType[1] != null){
                $this->compareComplexType(trim($prevType[1],'[]'));
            }
        }
        // Compare ComplexType with all
        else{
            $prevComplexTypeElement = self::$_prevWsdl-> xpath('//c:complexType[@name="' . $typeName . '"]//c:element');
            foreach($prevComplexTypeElement as $element){
                // Found 'element' by element 'name' in current wsdl
                $currComplexTypeElement = self::$_currWsdl-> xpath('//c:complexType[@name="' .
                    $typeName . '"]//c:element[@name="' . (string)$element['name'] . '"]');
                // Check element 'name' key in current wsdl
                $this->assertNotEmpty($currComplexTypeElement,
                    '\'Complex Type\': ' . $typeName . ' \'element\': ' . (string)$element['name'] .
                        ' was not fount in current wsdl.');
                // Check element 'type' key
                $this->assertEquals((string)$element['type'], (string)$currComplexTypeElement[0]['type'],
                    '\'Complex Type\': ' . $typeName . ' \'element\': ' . (string)$element['name'] .
                     ' with \'type\'= ' . (string)$element['type'] . ' was not fount in current wsdl.');
                // If minOccurc key presented in element, compare its values
                if (!is_null($element['minOccurs'])) {
                    $this->assertEquals((string)$element['minOccurs'], (string)$currComplexTypeElement[0]['minOccurs'],
                        '\'Complex Type\': ' . $typeName . ' \'element\': ' . (string)$element['name'] .
                        ' with \'minOccurs\' = ' . (string)$element['minOccurs'] . ' was not fount in current wsdl.');
                }
                // If 'arrayType' == typens:typeName ->  compareComplexType($typeName)
                $prevElementType = explode(':', (string)$element['type']);
                if ($prevElementType[0] == 'typens' && $prevElementType[1] != null){
                    $this->compareComplexType($prevElementType[1]);
                }
            }
        }
    }
}
