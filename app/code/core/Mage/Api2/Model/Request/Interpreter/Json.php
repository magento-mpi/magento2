<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Request content interpreter JSON adapter
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request_Interpreter_Json implements Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * Parse Request body into array of params
     *
     * @param string $body  Posted content from request
     * @return array|null   Return NULL if content is invalid
     * @throws Exception|Mage_Api2_Exception
     */
    public function interpret($body)
    {
        if (!is_string($body)) {
            throw new Exception(sprintf('Invalid data type "%s". String expected.', gettype($body)));
        }

        try {
            $decoded = Zend_Json::decode($body);
        } catch (Zend_Json_Exception $e) {
            throw new Mage_Api2_Exception('Decoding error.', Mage_Api2_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }
        if ($body != 'null' && $decoded === null) {
            throw new Mage_Api2_Exception('Decoding error.', Mage_Api2_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }

        return $decoded;
    }
}
