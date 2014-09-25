<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Framework\Object;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\FileSystem;
use Magento\Framework\View\TemplateEnginePool;

/**
 * Class Json
 */
class Json implements ContentTypeInterface
{
    /**
     * @var \Magento\Framework\View\FileSystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\View\TemplateEnginePool
     */
    protected $templateEnginePool;

    /**
     * Constructor
     *
     * @param FileSystem $filesystem
     * @param TemplateEnginePool $templateEnginePool
     */
    public function __construct(FileSystem $filesystem, TemplateEnginePool $templateEnginePool)
    {
        $this->filesystem = $filesystem;
        $this->templateEnginePool = $templateEnginePool;
    }

    /**
     * Render data
     *
     * @param UiComponentInterface $view
     * @param string $template
     * @return string
     */
    public function render(UiComponentInterface $view, $template = '')
    {
        return $view->getRenderContext()
            ->getConfigurationBuilder()
            ->toJson($view->getRenderContext()->getStorage(), $view->getName());
    }
}
