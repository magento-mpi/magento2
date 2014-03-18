<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Adminhtml\Product\Edit;

class Tab extends \Magento\Backend\Block\Widget\Tab
{
    /**
     * Module manager model
     *
     * @var \Magento\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Module\Manager $moduleManager,
        array $data = array()
    ) {
        $this->moduleManager = $moduleManager;

        parent::__construct($context, $data);

        if (!$this->_request->getParam('id') || !$this->_authorization->isAllowed('Magento_Review::reviews_all')) {
             $this->setCanShow(false);
        }
    }
}
