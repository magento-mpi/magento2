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

/**
 * Class Write
 * @package Magento\Doc\Controller\Index
 */
class Write extends AbstractAction
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
     * {@inheritdoc}
     */
    public function execute()
    {
        $action = $this->_request->getParam('action');
        $content = $this->_request->getParam('content');
        $type = $this->_request->getParam('type', 'xhtml');
        $module = $this->_request->getParam('module', 'Magento_Doc');
        $name = $this->_request->getParam('name');
        $scheme = $this->_request->getParam('scheme');
        switch ($action) {
            case 'save':
                echo $this->processContent($content, $type, $module, $scheme, $name);
                break;
        }
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
        $content = html_entity_decode($content);
        if ($type === 'xhtml') {
            $content = "<div>\n" . $content . "\n</div>";
        }
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
