<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Ui\ViewInterface;
use Magento\Framework\View\FileSystem;
use Magento\Framework\View\TemplateEnginePool;

/**
 * Class Html
 */
class Html implements ContentTypeInterface
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
     * @param ViewInterface $view
     * @param string $template
     * @return string
     */
    public function render(ViewInterface $view, $template = '')
    {
        $templateEngine = false;
        if ($template) {
            $extension = pathinfo($template, PATHINFO_EXTENSION);
            $templateEngine = $this->templateEnginePool->get($extension);
        }

        if ($templateEngine) {
            $extension = pathinfo($template, PATHINFO_EXTENSION);
            $templateEngine = $this->templateEnginePool->get($extension);
            $path = $this->filesystem->getTemplateFileName($template);
            $result = $templateEngine->render($view, $path);
        } else {
            $result = '';
        }

        return $result;
    }
}
