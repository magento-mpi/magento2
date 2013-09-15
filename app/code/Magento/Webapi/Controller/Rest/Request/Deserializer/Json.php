<?php
/**
 * JSON deserializer of REST request content.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Request\Rest\Interpreter;

class Json implements
    \Magento\Webapi\Controller\Request\Rest\DeserializerInterface
{
    /** @var Magento_Core_Helper_Data */
    protected $_helper;

    /** @var \Magento\Core\Model\App */
    protected $_app;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Core_Helper_Data $helper
     * @param \Magento\Core\Model\App $app
     */
    public function __construct(Magento_Core_Helper_Data $helper, Magento_Core_Model_App $app)
    {
        $this->_helper = $helper;
        $this->_app = $app;
    }

    /**
     * Parse Request body into array of params.
     *
     * @param string $encodedBody Posted content from request.
     * @return array|null Return NULL if content is invalid.
     * @throws \InvalidArgumentException
     * @throws \Magento\Webapi\Exception If decoding error was encountered.
     */
    public function deserialize($encodedBody)
    {
        if (!is_string($encodedBody)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" data type is invalid. String is expected.',
                gettype($encodedBody)
            ));
        }
        try {
            $decodedBody = $this->_helper->jsonDecode($encodedBody);
        } catch (\Zend_Json_Exception $e) {
            if (!$this->_app->isDeveloperMode()) {
                throw new \Magento\Webapi\Exception(__('Decoding error.'));
            } else {
                throw new \Magento\Webapi\Exception(
                    __('Decoding error: %1%2%3%4', PHP_EOL, $e->getMessage(), PHP_EOL, $e->getTraceAsString())
                );
            }
        }
        return $decodedBody;
    }
}
