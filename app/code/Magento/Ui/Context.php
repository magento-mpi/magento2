<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\Registry;
use Magento\Backend\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class Context
 */
class Context extends Registry
{
    /**
     * Data helper
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $dataHelper;

    /**
     * Application request
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * Accept type
     *
     * @var string
     */
    protected $acceptType;

    /**
     * Constructor
     *
     * @param TemplateContext $context
     * @param Data $dataHelper
     */
    public function __construct(TemplateContext $context, Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
        $this->request = $context->getRequest();
        $this->setAcceptType();
    }

    /**
     * Getting requested accept type
     *
     * @return string
     */
    protected function setAcceptType()
    {
        $this->acceptType = 'xml';

        $rawAcceptType = $this->request->getHeader('Accept');
        if (strpos($rawAcceptType, 'json') !== false) {
            $this->acceptType = 'json';
        } elseif (strpos($rawAcceptType, 'html') !== false) {
            $this->acceptType = 'html';
        }
    }

    /**
     * Getting accept type
     *
     * @return string
     */
    public function getAcceptType()
    {
        return $this->acceptType;
    }

    /**
     * Getting all request data
     *
     * @return mixed
     */
    public function getRequestParams()
    {
        return $this->request->getParams();
    }

    /**
     * Getting data according to the key
     *
     * @param string $key
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function getRequestParam($key, $defaultValue = null)
    {
        return $this->request->getParam($key, $defaultValue);
    }

    /**
     * Get data collection object
     *
     * @param string $key
     * @return \Magento\Framework\Data\Collection
     */
    public function getDataCollection($key)
    {
        return $this->registry($key);
    }

    /**
     * Set data collection
     *
     * @param string $key
     * @param \Magento\Framework\Data\Collection $dataCollection
     * @return void
     */
    public function setDataCollection($key, \Magento\Framework\Data\Collection $dataCollection)
    {
        $this->register($key, $dataCollection);
    }

    /**
     * Get filter data
     *
     * @param string $filterVar
     * @return array
     */
    public function getFilterData($filterVar)
    {
        $result = [];
        $filterString = $this->request->getParam($filterVar);
        if (!empty($filterString)) {
            $result = $this->dataHelper->prepareFilterString($this->request->getParam($filterVar));
        }

        return $result;
    }

    /**
     * Set meta fields
     *
     * @param string $key
     * @param array $data
     */
    public function setMeta($key, array $data)
    {
        $this->register($key . '_meta', $data);
    }

    /**
     * Get meta fields data
     *
     * @param string $key
     * @return array
     */
    public function getMeta($key)
    {
        return $this->registry($key . '_meta');
    }
}
