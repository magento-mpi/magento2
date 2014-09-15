<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Index;

use Magento\Doc\App\Controller\AbstractAction;

/**
 * Class Read
 * @package Magento\Doc\Controller\Dictionary
 */
class Read extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_view->loadLayout(['default', $this->getDocSchemeLayoutHandleName()]);

        $this->_view->renderLayout();
    }

    /**
     * Build layout handle name based on Documentation Scheme name
     *
     * @return string
     */
    protected function getDocSchemeLayoutHandleName()
    {
        $docScheme = $this->_request->getParam('doc_scheme', 'index');
        return str_replace('/', '_', strtolower($docScheme));
    }
}
