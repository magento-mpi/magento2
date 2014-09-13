<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Framework\Object;
use Magento\Ui\ViewInterface;
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
     * @param FileSystem $filesystem
     * @param TemplateEnginePool $templateEnginePool
     */
    public function __construct(FileSystem $filesystem, TemplateEnginePool $templateEnginePool)
    {
        $this->filesystem = $filesystem;
        $this->templateEnginePool = $templateEnginePool;
    }

    /**
     * @param ViewInterface $view
     * @param string $template
     * @return string
     */
    public function render(ViewInterface $view, $template = '')
    {
        return json_encode($this->getDataJson($view));
    }

    /**
     * @param ViewInterface $view
     * @return array
     */
    protected function getDataJson(ViewInterface $view)
    {
        return $view->getGlobalConfigJson();
    }

}
