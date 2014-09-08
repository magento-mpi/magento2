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
use Magento\Framework\Object;

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
        $templateEngine = false;
        if ($template) {
            $extension = pathinfo($template, PATHINFO_EXTENSION);
            $templateEngine = $this->templateEnginePool->get($extension);
        }
        if ($templateEngine) {
            $path = $this->filesystem->getTemplateFileName($template);
            $result = $templateEngine->render($view, $path);
        } else {
            $result = json_encode($this->getDataJson($view));
        }

        return $result;
    }

    /**
     * @param ViewInterface $view
     * @return array
     */
    protected function getDataJson(ViewInterface $view)
    {
        $result = [
            'configuration' => $view->getViewConfiguration(),
            'data' => []
        ];
        foreach ($view->getViewData() as $key => $value) {
            if (is_object($value)) {
                if (method_exists($value, 'toJson')) {
                    $result['data'][$key] = $value->toJson();
                } else {
                    $result['data'][$key] = $this->objectToJson($value);
                }
            } else {
                $result['data'][$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @param Object $object
     * @return string
     */
    protected function objectToJson(Object $object)
    {
        return '[object]';
    }
}
