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
     * Wsdl from previous API version
     * @var SimpleXMLElement
     */
    protected static $_prevWsdl;

    /**
     * Wsdl from current API version
     * @var SimpleXMLElement
     */
    protected static $_currWsdl;

    /**
     * Xpath constans
     */
    const OPERATIONS = '//wsdl:portType/wsdl:operation';
    const OPERATION_NAME ='//wsdl:portType/wsdl:operation[@name="%s"]';
    const OPERATION_INPUT = '//wsdl:portType/wsdl:operation/wsdl:input[@message="%s"]';
    const OPERATION_OUTPUT = '//wsdl:portType/wsdl:operation/wsdl:output[@message="%s"]';
    const MESSAGE = '//wsdl:message[@name="%s"]';
    const MESSAGE_PART_NAME = '//wsdl:message[@name="%s"]/wsdl:part[@name="%s"]';
    const COMPLEX_TYPE = '//xsd:complexType[@name="%s"]';
    const ELEMENTS = '//xsd:complexType[@name="%s"]//xsd:element';
    const ELEMENT ='//xsd:complexType[@name="%s"]//xsd:element[@name="%s"]';
    const COMPLEX_CONTENT_ATTRIBUTE = '//xsd:complexType[@name="%s"]/xsd:complexContent/xsd:restriction/xsd:attribute';
    const WSDL_NAMESPACE = 'http://schemas.xmlsoap.org/wsdl/';
    const COMPLEX_TYPE_NAMESPACE = 'http://www.w3.org/2001/XMLSchema';

    /**
     * Set up values for previous and current wsdl.
     *
     * Scenario:
     * 1. Load wsdl on previous API.
     * 2. Load wsdl on current API.
     * 3. Set Namespase. Note: simplexml will work incorrect if Namespase is not set.
     */

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_prevWsdl = simplexml_load_file(TESTS_PREV_WEBSERVICE_URL . "/api/v2_soap/?wsdl");
        self::$_currWsdl = simplexml_load_file(TESTS_WEBSERVICE_URL . "/api/v2_soap/?wsdl");
        // Register Namespace for 'messages' and 'operations'
        self::$_currWsdl->registerXPathNamespace("wsdl", self::WSDL_NAMESPACE);
        self::$_prevWsdl->registerXPathNamespace("wsdl", self::WSDL_NAMESPACE);
        // Register Namespace for 'complex type'
        self::$_currWsdl->registerXPathNamespace("xsd", self::COMPLEX_TYPE_NAMESPACE);
        self::$_prevWsdl->registerXPathNamespace("xsd", self::COMPLEX_TYPE_NAMESPACE);
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
       // Load array of all 'operation' on current wsdl
       $prevOperations = self::$_prevWsdl->xpath(self::OPERATIONS);
       // Check that operation array loaded and expected type = SimpleXMLElement
       $this->assertInternalType('array', $prevOperations, 'Loaded operations type is incorrect.');
       $this->assertNotEmpty($prevOperations, 'Previous wsdl operations was not loaded.');

       foreach ($prevOperations as $operation) {
           // Finds and compares 'operation['name']'
           $currOperation = self::$_currWsdl->xpath(sprintf(self::OPERATION_NAME, $operation['name']));
           $this->assertNotEmpty($currOperation, ' Operation: "' . $operation['name']
               . '" was not found in current wsdl.');
           // Finds and compares 'input['message']' data
           $foundInputElement = self::$_currWsdl->xpath(sprintf(self::OPERATION_INPUT,
               (string)$operation->input['message']));

           $this->assertNotEmpty($foundInputElement, 'Input element: "' . (string)$operation->input['message']
               . '" in operation: "' . $operation['name'] . '" was not found in current wsdl.');
           $this->compareMessagePart(str_replace('typens:', '', (string)$operation->input['message']));

            // Finds and compares 'output['message']' data
           $foundOutputElement = self::$_currWsdl->xpath(sprintf(self::OPERATION_OUTPUT,
               (string)$operation->output['message']));
           $this->assertNotEmpty($foundOutputElement,
               'Output element: "' . (string)$operation->output['message']
               . '" in operation "' . $operation['name'] . '" was not found in current wsdl.');

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
        $this->assertNotEmpty($foundCurrMessage, 'Message: "' . $messageName . '" was not found in current wsdl.');
        $this->assertNotEmpty($foundPrevMessage, 'Message: "' . $messageName . '" was not found in previous wsdl.');

        // Check part['name'] and part['type']
        $prevMessage = reset($foundPrevMessage);
        if (property_exists($prevMessage, 'part')) {
            foreach ($prevMessage->part as $prevPart) {
                // Check if there is 'part' attribute in 'message' on current wsdl
                $currMessagePart = self::$_currWsdl->xpath(sprintf(self::MESSAGE_PART_NAME,
                    $messageName, $prevPart['name']));
                $this->assertInternalType('array', $currMessagePart);
                $currPart = reset($currMessagePart);
                $this->assertNotEmpty($currPart, 'Message part: "' . $prevPart['name']
                    . '" was not found in message: "' . $messageName . '" in current wsdl.');
                // Check that parts 'name' and 'type' are equal
                $this->assertEquals((string)$prevPart['type'], (string)$currPart['type'],
                    'Message type: "' . $prevPart['type'] . '" was not found in message: "'
                    . $messageName . '" part:"' . $prevPart['name'] . '" in current wsdl.');

                list($typeNamespace, $typeName) = explode(':', $prevPart['type']);

                if ($typeNamespace == 'typens' && $typeName != null) {
                    $this->compareComplexType($typeName);
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
        // We don't check $typeName = 'anyType'
        if ($typeName == 'anyType') {
            return;
        }

        // Load object 'type' by name = $typeName
        $foundPrevComplexType = self::$_prevWsdl->xpath(sprintf(self::COMPLEX_TYPE, $typeName));
        $foundCurrComplexType = self::$_currWsdl->xpath(sprintf(self::COMPLEX_TYPE, $typeName));

        // Check that 'type' with name = $typeName presented in current wsdl
        $this->assertNotEmpty($foundCurrComplexType,
            'Complex Type: "' . $typeName . '" was not found in current wsdl.');

        // Check 'type' description in case if in previous wsdl 'type' presented but not described
        $this->assertNotEmpty($foundPrevComplexType,
            'Complex Type: "' . $typeName . '" was not found in previous wsdl.');

        // Compare ComplexType with ComplexContent
        if (key_exists('complexContent', reset($foundPrevComplexType))) {
            // Load value for 'arrayType' in ComplexContent 'attribute' that is in spesific namespace
            $prevAttributeType = self::$_prevWsdl->xpath(sprintf(self::COMPLEX_CONTENT_ATTRIBUTE, $typeName));

            // Check that previous wsdl contains $typeName description
            $this->assertNotEmpty($prevAttributeType, 'Complex Type: "' . $typeName
                . '" was not found but was mentioned in operation in previous wsdl.');
            $prevType = reset($prevAttributeType)->attributes(self::WSDL_NAMESPACE);

            // Check that current Attribute still has 'complexContent' key
            $this->assertObjectHasAttribute('complexContent', reset($foundCurrComplexType), 'Complex Type: "'
                . $typeName . '" does not have "complexContent" attribute in current wsdl.');
            // Load ComplexType "type" from current wsdl
            $currAttributeType = self::$_currWsdl->xpath(sprintf(self::COMPLEX_CONTENT_ATTRIBUTE, $typeName));
            $currType = reset($currAttributeType)->attributes(self::WSDL_NAMESPACE);

            // Compare found values for 'arrayType'
            $this->assertEquals((string)$prevType['arrayType'], (string)$currType['arrayType'],
                'Complex Type: "' . $typeName . '" with attribute[arrayType] = "' . (string)$prevType['arrayType']
                . '" was not found in current wsdl.');

            // If 'arrayType' == typens:typeName ->  compareComplexType($typeName)
            list($complexTypeNamespace, $complexTypeName) = explode(':', (string)$prevType['arrayType']);

            if ($complexTypeNamespace == 'typens' && $complexTypeName != null) {
                $this->compareComplexType(trim($complexTypeName, '[]'));
            }
        } else {
            // Compare ComplexType with all
            $prevComplexTypeElement = self::$_prevWsdl->xpath(sprintf(self::ELEMENTS, $typeName));
            foreach($prevComplexTypeElement as $element) {
                // Found 'element' by element 'name' in current wsdl
                $currComplexTypeElement = self::$_currWsdl-> xpath(sprintf(self::ELEMENT, $typeName,
                    (string)$element['name']));
                $currType = reset($currComplexTypeElement);
                // Check element 'name' key in current wsdl
                $this->assertNotEmpty($currType,
                    'Complex Type: "' . $typeName . '" with element[name]: "' . (string)$element['name']
                    . '" was not fount in current wsdl.');
                // Check element 'type' key in current wsdl
                $this->assertEquals((string)$element['type'], (string)$currType['type'],
                    'Complex Type: "' . $typeName . '" with element[name]: "' . (string)$element['name']
                     . '" and element[type]= "' . (string)$element['type'] . '" was not fount in current wsdl.');
                // If 'minOccurs' key presented in 'element', compare its values
                if (!is_null($element['minOccurs'])) {
                    $this->assertEquals((string)$element['minOccurs'], (string)$currType['minOccurs'],
                        'Complex Type: "' . $typeName . '" with element[name]: "' . (string)$element['name']
                        . '" and element[minOccurs]: "' . (string)$element['minOccurs']
                        . '" was not fount in current wsdl.');
                }
                // If 'arrayType' == typens:typeName ->  compareComplexType($typeName)
                list($complexTypeNamespace, $complexTypeName) = explode(':', (string)$element['type']);

                if ($complexTypeNamespace == 'typens' && $complexTypeName != null) {
                    $this->compareComplexType($complexTypeName);
                }
            }
        }
    }
}
