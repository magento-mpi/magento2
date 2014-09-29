<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\DataProvider\Factory as DataProviderFactory;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class Form
 */
class Form extends AbstractView
{
    /**
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configFactory
     * @param ConfigBuilderInterface $configBuilder
     * @param DataProviderFactory $dataProviderFactory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configFactory,
        ConfigBuilderInterface $configBuilder,
        DataProviderFactory $dataProviderFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $renderContext,
            $contentTypeFactory,
            $configFactory,
            $configBuilder,
            $dataProviderFactory,
            $data
        );
    }

    public function prepare()
    {
        $config = $this->configFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
            ]
        );

        $this->setConfig($config);
        $this->renderContext->getStorage()->addComponentsData($config);

        $provider = $this->factoryProvider->get($this->getData('dataProvider'));

        $this->renderContext->getStorage()->addMeta($this->getName(), $this->getData('meta'));
        $this->renderContext->getStorage()->addData($this->getName(), $provider->getData());
        parent::prepare();
    }

    /**
     * @TODO Fix it
     *
     * @return array|null
     */
    public function getMeta()
    {
        return $this->renderContext->getStorage()->getMeta($this->getName());
    }

    /**
     * @TODO Fix it
     *
     * @return array|null
     */
    public function getProviderData()
    {
        return $this->renderContext->getStorage()->getData($this->getParentName());
    }

    public function getCssClass()
    {

    }
}
