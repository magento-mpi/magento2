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
     * Array of all errors during comparing previous and current wsdl
     * @var array
     */
    protected $_errors = array();

    /**
     * Ignore list has the same structure as the previous wsdl.
     * It contains all known issue
     * @var SimpleXMLElement
     */
    protected static $_ignoreWsdl;

    /**
     * Initialization before all tests
     */
    protected function setUp()
    {
        $_errors = null;
        parent::setUp();
    }

    /**
     * Xpath constants
     */
    const OPERATIONS = '//wsdl:portType/wsdl:operation';
    const OPERATION_NAME = '//wsdl:portType/wsdl:operation[@name="%s"]';
    const OPERATION_INPUT = '//wsdl:portType/wsdl:operation/wsdl:input[@message="%s"]';
    const OPERATION_OUTPUT = '//wsdl:portType/wsdl:operation/wsdl:output[@message="%s"]';
    const MESSAGE = '//wsdl:message[@name="%s"]';
    const MESSAGE_PART_NAME = '//wsdl:message[@name="%s"]/wsdl:part[@name="%s"]';
    const COMPLEX_TYPE = '//xsd:complexType[@name="%s"]';
    const ELEMENTS = '//xsd:complexType[@name="%s"]//xsd:element';
    const ELEMENT = '//xsd:complexType[@name="%s"]//xsd:element[@name="%s"]';
    const COMPLEX_CONTENT_ATTRIBUTE = '//xsd:complexType[@name="%s"]/xsd:complexContent/xsd:restriction/xsd:attribute';
    const WSDL_NAMESPACE = 'http://schemas.xmlsoap.org/wsdl/';
    const COMPLEX_TYPE_NAMESPACE = 'http://www.w3.org/2001/XMLSchema';

    /**
     * Set up values for previous and current wsdl.
     *
     * Scenario:
     * 1. Load wsdl on previous API.
     * 2. Load wsdl on current API.
     * 3. Set Namespace. Note: simplexml will work incorrect if Namespace is not set.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        // Loading wsdl on current and previous wsdl
        self::$_prevWsdl = simplexml_load_file(TESTS_PREV_WEBSERVICE_URL . "/api/v2_soap/?wsdl");
        self::$_currWsdl = simplexml_load_file(TESTS_WEBSERVICE_URL . "/api/v2_soap/?wsdl");
        // Loading ignore list that has the same structure as wsdl
        self::$_ignoreWsdl = simplexml_load_file(__DIR__ . '/_files/ignore.xml');
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
     */
   public function testWsdlCompatibility()
   {
       // Load array of all 'operation' on current wsdl
       $prevOperations = self::$_prevWsdl->xpath(self::OPERATIONS);
       // Check that operation array is loaded and expected type = array
       $this->assertInternalType('array', $prevOperations, 'Loaded operations type is incorrect.');
       $this->assertNotEmpty($prevOperations, 'Previous wsdl operations was not loaded.');

       foreach ($prevOperations as $operation) {
           // Finds and compares 'operation['name']'
           $operationXpath = sprintf(self::OPERATION_NAME, $operation['name']);
           $currOperation = self::$_currWsdl->xpath($operationXpath);
           try {
               $this->assertNotEmpty($currOperation, 'Operation: "' . $operation['name']
                   . '" was not found in current wsdl.');
           } catch (Exception $e) {
               if (!$this->elementIgnored($operationXpath . '[@ignored="1"]')) {
                   $this->_errors['operationErrors'][] = $e->getMessage();
               }
               continue;
           }

           // Finds and compares 'input['message']' data
           $operationInputXpath = sprintf(self::OPERATION_INPUT, (string)$operation->input['message']);
           $foundInputElement = self::$_currWsdl->xpath($operationInputXpath);
           try {
               $this->assertNotEmpty($foundInputElement, 'Input element: "' . (string)$operation->input['message']
                   . '" in operation: "' . $operation['name'] . '" was not found in current wsdl.');
               // Comparing input message part and message data
               $this->compareMessagePart(str_replace('typens:', '', (string)$operation->input['message']));
           } catch (Exception $e) {
               if(!$this->elementIgnored($operationInputXpath)) {
                   $this->_errors['operationErrors'][] = $e->getMessage();
               }
           }

           // Finds and compares 'output['message']' data
           $operationOutputXpath = sprintf(self::OPERATION_OUTPUT, (string)$operation->output['message']);
           $foundOutputElement = self::$_currWsdl->xpath($operationOutputXpath);
           try {
               $this->assertNotEmpty($foundOutputElement, 'Output element: "' . (string)$operation->output['message']
                   . '" in operation "' . $operation['name'] . '" was not found in current wsdl.');
               // Comparing output message part and message data
               $this->compareMessagePart(str_replace('typens:', '', (string)$operation->output['message']));
           } catch (Exception $e) {
               if(!$this->elementIgnored($operationOutputXpath)) {
                    $this->_errors['operationErrors'][] = $e->getMessage();
               }
           }
       }
       // Check _errors and if it not empty, test will failed with all error messages
       $this->assertEmpty($this->_errors, $this->implodeErrorArrayToString($this->_errors));
   }

    /**
     * Check message part of operation. Works with input and output message
     * @param $messageName
     */
    protected function compareMessagePart($messageName)
    {
        // Load operation messages
        $messageXpath = sprintf(self::MESSAGE, $messageName);
        $foundCurrMessage = self::$_currWsdl->xpath($messageXpath);
        $foundPrevMessage = self::$_prevWsdl->xpath($messageXpath);
        try {
            // Check that messages is loaded in both wsdl
            $this->assertNotEmpty($foundCurrMessage, 'Message: "' . $messageName . '" was not found in current wsdl.');
            $this->assertNotEmpty($foundPrevMessage, 'Message: "' . $messageName . '" was not found in previous wsdl.');
            // Check part['name'] and part['type']
            $prevMessage = current($foundPrevMessage);
            if (property_exists($prevMessage, 'part')) {
                foreach ($prevMessage->part as $prevPart) {
                    // Check if there is 'part' attribute in 'message' on current wsdl
                    $messagePartXpath = sprintf(self::MESSAGE_PART_NAME, $messageName, $prevPart['name']);
                    $currMessagePart = self::$_currWsdl->xpath($messagePartXpath);
                    $this->assertInternalType('array', $currMessagePart);
                    $currPart = current($currMessagePart);
                    // Check that part['name'] was founded in current wsdl
                    if (!empty($currPart)) {
                        try {
                            // Check that part['type'] are equal in both wsdl
                            $prevPartType = (string)$prevPart['type'];
                            $this->assertEquals($prevPartType, (string)$currPart['type'],
                                'Message type: "' . $prevPartType . '" was not found in message: "'
                                . $messageName . '" part:"' . $prevPart['name'] . '" in current wsdl.');
                            // If part['type'] is ComplexType, compare its using compareComplexType()
                            list($typeNamespace, $typeName) = explode(':', $prevPartType);

                            if ($typeNamespace == 'typens' && $typeName != null && $typeName != 'anyType') {
                                $this->compareComplexType($typeName);
                            }
                        } catch (Exception $e) {
                            if (!$this->elementIgnored($messagePartXpath . '[@typeIgnored="1"]')) {
                                $this->_errors['messageErrors'][] = $e->getMessage();
                            }
                        }
                    } else if(!$this->elementIgnored($messagePartXpath . '[@nameIgnored="1"]')) {
                        $this->_errors['messageErrors'][] = 'Message part: "' . $prevPart['name']
                            . '" was not found in message: "' . $messageName . '" in current wsdl.';
                    }
                }
            }
        } catch (Exception $e) {
            if (!$this->elementIgnored($messageXpath . '[@ignored="1"]')) {
                $this->_errors['messageErrors'][] = $e->getMessage();
            }
        }
    }

    /**
     * Comparing 'message' ComplexType 'type'
     *
     * @param $typeName
     * @param $parentTypeName
     */
    protected function compareComplexType($typeName, $parentTypeName = null)
    {
        $complexTypeXpath = sprintf(self::COMPLEX_TYPE, $typeName);
        // Load object 'type' by name = $typeName
        $foundPrevComplexType = self::$_prevWsdl->xpath($complexTypeXpath);
        $foundCurrComplexType = self::$_currWsdl->xpath($complexTypeXpath);
        try {
            // Check that 'type' with name = $typeName presents in current wsdl
            $this->assertNotEmpty($foundCurrComplexType,
                'Complex Type: "' . $typeName . '" was not found in current wsdl.');
            // Check 'type' description in case if in previous wsdl 'type' presented but not described
            $this->assertNotEmpty($foundPrevComplexType,
                'Complex Type: "' . $typeName . '" was not found in previous wsdl.');

            if (array_key_exists('complexContent', current($foundPrevComplexType))) {
                try {
                    // Check that current wsdl complexType still has 'complexContent' key
                    $this->assertObjectHasAttribute('complexContent', current($foundCurrComplexType), 'Complex Type: "'
                        . $typeName . '" does not have "complexContent" attribute in current wsdl.');

                    // Compare ComplexType with <ComplexContent> key
                    $this->compareComplexTypeComplexContent($typeName, $parentTypeName);
                } catch (Exception $e) {
                    $this->_errors['complexTypeErrors'][$typeName] = $e->getMessage();
                }
            } else {
                // Compare ComplexType with <all> key
                $this->compareComplexTypeElement($typeName, $parentTypeName);
            }
        } catch (Exception $e) {
            if(!$this->elementIgnored($complexTypeXpath . '[@ignored="1"]')) {
                $this->_errors['complexTypeErrors'][$typeName] = $e->getMessage();
            }
        }
    }

    /**
     * Compare ComplexType types of API responses (current and previous versions)
     * that contan <all> key with <elements>
     *
     * @param $typeName
     * @param null $parentTypeName
     */
    protected function compareComplexTypeElement($typeName, $parentTypeName = null)
    {
        $prevComplexTypeElement = self::$_prevWsdl->xpath(sprintf(self::ELEMENTS, $typeName));
        foreach ($prevComplexTypeElement as $element) {
            // Search 'element' by element 'name' in current wsdl
            $elementType = (string)$element['type'];
            $elementName = (string)$element['name'];
            $elementXpath = sprintf(self::ELEMENT, $typeName, $elementName);
            $currComplexTypeElement = self::$_currWsdl->xpath($elementXpath);
            $currType = current($currComplexTypeElement);

            // Check element 'name' key in current wsdl
            if(!empty($currType)) {
                try {
                    // Check element 'type' key in current wsdl
                    $this->assertEquals($elementType, (string)$currType['type'],
                        'Complex Type: "' . $typeName . '" with element[name]: "'
                        . $elementName . '" and element[type]= "' . $elementType
                        . '" was not fount in current wsdl.');

                    // If 'arrayType' == typens:typeName ->  compareComplexType($typeName, $messageName)
                    list($complexTypeNamespace, $complexTypeName) = explode(':', $elementType);
                    $doRecursiveTypeCheck = !is_null($parentTypeName) ? $parentTypeName != $complexTypeName : true;
                    if ($complexTypeNamespace == 'typens' && $complexTypeName != null && $complexTypeName != 'anyType'
                        && $doRecursiveTypeCheck) {
                        $this->compareComplexType($complexTypeName);
                    }
                } catch (Exception $e) {
                    if(!$this->elementIgnored($elementXpath . '[@typeIgnored="1"]')) {
                        $this->_errors['complexTypeElementErrors'][$typeName][$elementType] =
                            $e->getMessage();
                    }
                }
                try {
                    // If 'minOccurs' key presented in 'element', compare its values
                    if (!is_null($element['minOccurs'])) {
                        $elementMinOccurs = (string)$element['minOccurs'];
                        $this->assertEquals($elementMinOccurs, (string)$currType['minOccurs'],
                            'Complex Type: "' . $typeName . '" with element[name]: "' . $elementName
                            . '" and element[minOccurs]: "' . $elementMinOccurs
                            . '" was not fount in current wsdl.');
                    }
                } catch (Exception $e) {
                    if (!$this->elementIgnored($elementXpath . '[@minOccursIgnored="1"]')) {
                        $this->_errors['complexTypeElementErrors'][$typeName][$elementMinOccurs] = $e->getMessage();
                    }
                }
            } else if (!$this->elementIgnored($elementXpath . '[@nameIgnored="1"]')) {
                $this->_errors['complexTypeElementErrors'][$typeName][$elementName] =
                    'Complex Type: "' . $typeName . '" with element[name]: "'
                    . $elementName . '" was not fount in current wsdl.';
            }
        }
    }

    /**
     * Compare ComplexType types of API responses (current and previous versions)
     * that contain <ComplexContent> key
     * @param string $typeName
     * @param string|null $parentTypeName
     */
     protected function compareComplexTypeComplexContent($typeName, $parentTypeName = null)
     {
         $attributeXpath = sprintf(self::COMPLEX_CONTENT_ATTRIBUTE, $typeName);
         try {
             // Load value for 'arrayType' in ComplexContent 'attribute'
             // that is in specific namespace in previous wsdl
             $prevAttributeType = self::$_prevWsdl->xpath($attributeXpath);
             // Check that previous wsdl contains Complex Type complexContent attribute
             $this->assertNotEmpty($prevAttributeType, 'Complex Type: "' . $typeName
                 . '" complexContent attribute is empty in previous wsdl.');
             $prevType = current($prevAttributeType)->attributes(self::WSDL_NAMESPACE);

             // Load value for 'arrayType' in ComplexContent 'attribute'
             // that is in specific namespace in current wsdl
             $currAttributeType = self::$_currWsdl->xpath($attributeXpath);
             // Check that current wsdl contains Complex Type complexContent attribute
             $this->assertNotEmpty($currAttributeType, 'Complex Type: "' . $typeName
                 . '" complexContent attribute is empty in current wsdl.');
             $currType = current($currAttributeType)->attributes(self::WSDL_NAMESPACE);

             // Compare found values for 'arrayType'
             $prevArrayType = (string)$prevType['arrayType'];
             $this->assertEquals($prevArrayType, (string)$currType['arrayType'],
                 'Complex Type: "' . $typeName . '" with attribute[arrayType] = "' . $prevArrayType
                 . '" was not found in current wsdl.');

             // If 'arrayType' == typens:typeName ->  compareComplexType($typeName, $messageName)
             list($complexTypeNamespace, $complexTypeName) = explode(':', $prevArrayType);
             $doRecursiveTypeCheck = !is_null($parentTypeName) ? $parentTypeName != $complexTypeName : true;
             if ($complexTypeNamespace == 'typens' && $complexTypeName != null && $complexTypeName != 'anyType'
                 && $doRecursiveTypeCheck) {
                 $this->compareComplexType(trim($complexTypeName, '[]'), $typeName);
             }
         } catch (Exception $e) {
             if(!$this->elementIgnored($attributeXpath . '[@ignored="1"]')) {
                 $this->_errors['complexTypeErrors'][$typeName] = $e->getMessage();
             }
         }
     }

    /**
     * This function implodes $errors to string, grouped by keys values
     *
     * @param $errors array
     * @return string
     */
    protected function implodeErrorArrayToString($errors)
    {
        $implodedErrors = "\n";

        foreach (array('operationErrors', 'messageErrors', 'complexTypeErrors') as $errorType) {
            if (isset($errors[$errorType])) {
                $implodedErrors .= implode("\n", $errors[$errorType]) . "\n";
            }
        }

        if (array_key_exists('complexTypeElementErrors', $errors)) {
            foreach ($errors['complexTypeElementErrors'] as $complexTypeElementErrors) {
                $implodedErrors .= implode("\n", $complexTypeElementErrors) . "\n";
            }
        }

        return $implodedErrors;
    }

    /**
     * Returns result of finding node by xpath in ignore file, that has the same structure as wsdl
     *
     * @param $xpath
     * @return bool
     */
    protected function elementIgnored($xpath)
    {
        // Finds wsdl element by $xpath
        $foundElement = self::$_ignoreWsdl->xpath($xpath);
        // Element has been ignored if it is founded
        return !empty($foundElement);
    }
}
