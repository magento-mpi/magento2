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
        $module = $this->_request->getParam('module', 'Magento_Doc');
        $name = $this->_request->getParam('name');
        $type = $this->_request->getParam('type', 'html');
        $content = $this->_request->getParam('content');
        switch ($action) {
            case 'save':
                echo $this->processContent($content, $type, $module, $name);
                break;
        }
    }

    /**
     * @param string $content
     * @param string $type
     * @param string $module
     * @param string $name
     * @return string
     */
    protected function processContent($content, $type, $module, $name)
    {
        $content = trim($content, "\n");
        $content = html_entity_decode($content);
        if ($module && $name) {
            $path = str_replace('_', '/', $module) . '/docs/content/' . str_replace('_', '/', $name) . '.' . $type;
            $this->moduleDir->writeFile($path, $content);
        }
        $block = $this->_view->getLayout()->createBlock('Magento\Framework\View\Element\Template');
        $templateEngine = $this->enginePool->get($type);
        $html = $templateEngine->render($block, $content);
        return $html;
    }
}
