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

        $this->_title->add($variable->getId() ? $variable->getCode() : __('New Custom Variable'));

        $resultPage = $this->createPage();
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
