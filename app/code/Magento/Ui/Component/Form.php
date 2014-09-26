<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\ContentType\ContentTypeFactory;

class Form extends AbstractView
{
    /**
     * @var \Magento\Ui\DataProvider\Factory
     */
    protected $factoryProvider;

    /**
     * @param TemplateContext $context
     * @param Context $renderContext
     * @param ContentTypeFactory $contentTypeFactory
     * @param ConfigFactory $configFactory
     * @param ConfigBuilderInterface $configBuilder
     * @param \Magento\Ui\DataProvider\Factory $factoryProvider
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Context $renderContext,
        ContentTypeFactory $contentTypeFactory,
        ConfigFactory $configFactory,
        ConfigBuilderInterface $configBuilder,
        \Magento\Ui\DataProvider\Factory $factoryProvider,
        array $data = []
    ) {
        $this->factoryProvider = $factoryProvider;
        parent::__construct($context, $renderContext, $contentTypeFactory, $configFactory, $configBuilder,
            $data);
    }

    public function prepare()
    {
        $this->configuration = $this->configurationFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
            ]
        );

        $this->renderContext->getStorage()->addComponentsData($this->configuration);

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
