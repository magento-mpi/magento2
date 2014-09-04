<?php
/**
 * {license}
 */

namespace Magento\Ui\ContentType;

use Magento\Ui\UiInterface;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Framework\View\FileSystem;

/**
 * Class Html
 * @package Magento\Ui\ContentType
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
     * @param FileSystem $filesystem
     * @param TemplateEnginePool $templateEnginePool
     */
    public function __construct(
        FileSystem $filesystem,
        TemplateEnginePool $templateEnginePool)
    {
        $this->filesystem = $filesystem;
        $this->templateEnginePool = $templateEnginePool;
    }

    /**
     * @param UiInterface $ui
     * @param array $data
     * @param array $configuration
     * @return string
     */
    public function render(UiInterface $ui, array $data, array $configuration)
    {
        $result = '';
        if (isset($configuration['template'])) {
            $extension = pathinfo($configuration['template'], PATHINFO_EXTENSION);
            $templateEngine = $this->templateEnginePool->get($extension);
            $path = $this->filesystem->getTemplateFileName($configuration['template']);
            $result = $templateEngine->render($ui, $path, $data);
        }
        return $result;
    }
}
