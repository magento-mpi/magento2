<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_view->loadLayout(['default', $this->getDocLayoutHandleName()]);

        $this->_view->renderLayout();
    }

    /**
     * Build layout handle name based on Documentation Name
     *
     * @return string
     */
    protected function getDocLayoutHandleName()
    {
        $docName = $this->_request->getParam('doc_name', 'index');
        return str_replace('/', '_', strtolower($docName));
    }
}
