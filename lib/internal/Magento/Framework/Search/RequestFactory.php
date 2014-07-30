<?php
/**
 * Search Request Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

class RequestFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Request Declaration entity
     *
     * @var \Magento\Framework\Search\Request\Config
     */
    protected $requestDeclaration;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param Request\Config $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Search\Request\Config $declaration
    ) {
        $this->objectManager = $objectManager;
        $this->requestDeclaration = $declaration;
    }

    /**
     * Create Request instance with specified parameters
     *
     * @param string $requestName
     * @param array $bindValues
     * @return \Magento\Framework\Search\Request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($requestName, array $bindValues = array())
    {
        /**
         * Bind Values Structure:
         * array(
         *     ['query' => 'query_name', 'field' => 'field_name', 'value' => 'value'],
         *     ['query' => 'query_name', 'field' => 'field_name', 'value' => 'value'],
         *     ...
         * )
         */
        $bindValues;
        /**
         * @todo Fill $data array here with $this->requestDeclaration
         */
        $data = array('name' => $requestName);
        return $this->objectManager->create('Magento\Framework\Search\Request', $data);
    }
}
