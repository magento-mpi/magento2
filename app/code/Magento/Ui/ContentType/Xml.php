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
use Magento\Framework\Xml\Generator;

/**
 * Class Xml
 */
class Xml implements ContentTypeInterface
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
     * @var \Magento\Framework\Xml\Generator
     */
    protected $generator;

    /**
     * @param FileSystem $filesystem
     * @param TemplateEnginePool $templateEnginePool
     * @param Generator $generator
     */
    public function __construct(FileSystem $filesystem, TemplateEnginePool $templateEnginePool, Generator $generator)
    {
        $this->filesystem = $filesystem;
        $this->templateEnginePool = $templateEnginePool;
        $this->generator = $generator;
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
            $path = $this->filesystem->getTemplateFileName($template);
            $result = $templateEngine->render($view, $path);
        } else {
            $result = $this->getDataXml($view);
        }
        return $result;
    }

    /**
     * @param ViewInterface $view
     * @return string
     */
    protected function getDataXml(ViewInterface $view)
    {
        $result = [
            'configuration' => $view->getRenderContext()->getStorage()->getComponentsData($view->getName())->getData(),
            'data' => []
        ];
        foreach ($view->getRenderContext()->getStorage()->getData($view->getName()) as $key => $value) {
            if (is_object($value)) {
                if (method_exists($value, 'toXml')) {
                    $result['data'][$key] = $value->toXml();
                } else {
                    $result['data'][$key] = $this->objectToXml($value);
                }
            } else {
                $result['data'][$key] = $value;
            }
        }
        return $this->generator->arrayToXml($result);
    }

    /**
     * Convert object to xml format
     *
     * @param \Magento\Framework\Object $object
     * @return string
     */
    protected function objectToXml(\Magento\Framework\Object $object)
    {
        return (string)$object;
    }
}
