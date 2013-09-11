<?php
/**
 * JSON interpreter of REST request content.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Request\Rest\Interpreter;

class Json implements
    \Magento\Webapi\Controller\Request\Rest\InterpreterInterface
{
    /** @var \Magento\Core\Model\Factory\Helper */
    protected $_helperFactory;

    /** @var \Magento\Core\Model\App */
    protected $_app;

    /**
     * @param \Magento\Core\Model\Factory\Helper $helperFactory
     * @param \Magento\Core\Model\App $app
     */
    public function __construct(\Magento\Core\Model\Factory\Helper $helperFactory, \Magento\Core\Model\App $app)
    {
        $this->_helperFactory = $helperFactory;
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
    public function interpret($encodedBody)
    {
        if (!is_string($encodedBody)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" data type is invalid. String is expected.',
                gettype($encodedBody)
            ));
        }
        try {
            /** @var \Magento\Core\Helper\Data $jsonHelper */
            $jsonHelper = $this->_helperFactory->get('Magento\Core\Helper\Data');
            $decodedBody = $jsonHelper->jsonDecode($encodedBody);
        } catch (\Zend_Json_Exception $e) {
            if (!$this->_app->isDeveloperMode()) {
                throw new \Magento\Webapi\Exception(__('Decoding error.'),
                    \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
            } else {
                throw new \Magento\Webapi\Exception(
                    'Decoding error: ' . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString(),
                    \Magento\Webapi\Exception::HTTP_BAD_REQUEST
                );
            }

        }
        return $decodedBody;
    }
}
