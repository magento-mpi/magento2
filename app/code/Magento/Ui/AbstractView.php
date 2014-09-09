<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Asset\Repository;
use Magento\Ui\ContentType\ContentTypeFactory;

/**
 * Class AbstractView
 */
abstract class AbstractView extends Template implements ViewInterface
{
    /**
     * @var ContentTypeFactory
     */
    protected $factory;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var array
     */
    protected $viewData = [];

    /**
     * @var array
     */
    protected $viewConfiguration = [];

    /**
     * @param Context $context
     * @param ContentTypeFactory $factory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ContentTypeFactory $factory,
        array $data = array())
    {
        $this->factory = $factory;
        $this->assetRepo = $context->getAssetRepository();
        parent::__construct($context, $data);
    }

    /**
     * @param array $arguments
     * @param string $acceptType
     * @param array $requestParams
     * @return mixed|string
     */
    public function render(array $arguments = [], $acceptType = 'html', array $requestParams = [])
    {
        $prevArgs = $this->_data;
        $this->_data = array_replace_recursive($this->_data, $arguments);
        $this->prepare();
        $result = $this->factory->get($acceptType)->render($this, $this->getTemplate());
        $this->_data = $prevArgs;
        return $result;
    }

    protected function prepare()
    {
        //
    }

    /**
     * Shortcut for rendering as HTML
     * (used for backward compatibility with standard rendering mechanism via layout interface)
     *
     * @return string
     */
    public function toHtml()
    {
        $acceptType = $this->getAcceptType();
        return $this->render([], $acceptType);
    }

    /**
     * Getting template
     *
     * @return string|false
     */
    public function getTemplate()
    {
        return $this->getData('template');
    }

    /**
     * Getting view data array
     *
     * @return array
     */
    public function getViewData()
    {
        return (array) $this->viewData;
    }

    /**
     * Getting configuration settings array
     *
     * @return array
     */
    public function getViewConfiguration()
    {
        return (array) $this->viewConfiguration;
    }

    /**
     * Getting JSON configuration data
     *
     * @return string
     */
    public function getConfigurationJson()
    {
        return json_encode($this->getViewConfiguration());
    }

    /**
     * @return ContentType\ContentTypeInterface
     */
    protected function getRenderEngine()
    {
        return $this->factory->get($this->getAcceptType());
    }

    /**
     * Getting requested accept type
     *
     * @return string
     */
    protected function getAcceptType()
    {
        $rawAcceptType = $this->_request->getHeader('Accept');
        if (strpos($rawAcceptType, 'json') !== false) {
            $acceptType = 'json';
        } else if (strpos($rawAcceptType, 'xml') !== false) {
            $acceptType = 'xml';
        } else {
            $acceptType = 'html';
        }
        return $acceptType;
    }

    /**
     * Getting name instance
     *
     * @return string
     */
    public function getName()
    {
        return $this->viewConfiguration['config']['name'];
    }
}
