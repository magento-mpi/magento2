<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Index;

use Magento\Doc\App\Controller\AbstractAction;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Framework\App\Filesystem;

class Preview extends AbstractAction
{
    /**
     * @var \Magento\Framework\View\TemplateEnginePool
     */
    protected $enginePool;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $moduleDir;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        TemplateEnginePool $enginePool,
        Filesystem $filesystem
    ) {
        $this->enginePool = $enginePool;
        $this->moduleDir = $filesystem->getDirectoryWrite(Filesystem::MODULES_DIR);
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $content = $this->_request->getParam('data');
        echo $content;
    }


    /**
     * @param string $content
     * @param string $type
     * @param string $module
     * @param string $scheme
     * @param string $name
     * @return string
     */
    protected function processContent($content, $type, $module, $scheme, $name)
    {
        $content = trim($content, "\n");
        if ($module && $scheme && $name) {
            $path = str_replace('_', '/', $module) . '/docs/content/' . $scheme . '/' . $name . '.' . $type;
            $this->moduleDir->writeFile($path, $content);
        }
        $block = $this->_view->getLayout()->createBlock('Magento\Framework\View\Element\Template');
        $templateEngine = $this->enginePool->get($type);
        $html = $templateEngine->render($block, $content);
        return $html;
    }
}
