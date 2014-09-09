<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Controller\Adminhtml\Listing;

/**
 * Class Ajax
 */
class Ajax extends \Magento\Backend\App\Action
{
    /**
     * Action for AJAX request
     *
     * @return void
     */
    public function execute()
    {
        $this->_response->appendBody(
            $this->_view->getLayout()->createBlock(
                'Magento\Ui\Render',
                'root',
                [
                    'data' => [
                        'component' => $this->getComponent(),
                        'name' => $this->getName()
                    ]
                ]
            )->toHtml()
        );
    }

    /**
     * @return mixed
     */
    protected function getName()
    {
        return $this->_request->getParam('name');
    }

    /**
     * @return mixed
     */
    protected function getComponent()
    {
        return $this->_request->getParam('component');
    }
}
