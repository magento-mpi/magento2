<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Variable;

class Edit extends \Magento\Backend\Controller\Adminhtml\System\Variable
{
    /**
     * Edit Action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $variable = $this->_initVariable();

        $resultPage = $this->createPage();
        $resultPage->getConfig()->getTitle()->prepend(
            $variable->getId() ? $variable->getCode() : __('New Custom Variable')
        );
        $resultPage->addContent($resultPage->getLayout()->createBlock('Magento\Backend\Block\System\Variable\Edit'))
            ->addJs(
                $resultPage->getLayout()->createBlock(
                    'Magento\Framework\View\Element\Template',
                    '',
                    ['data' => ['template' => 'Magento_Backend::system/variable/js.phtml']]
                )
            );
        return $resultPage;
    }
}
