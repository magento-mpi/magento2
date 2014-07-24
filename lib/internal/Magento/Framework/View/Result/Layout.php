<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Element\Template;

/**
 * A generic layout response can be used for rendering any kind of layout
 * So it comprises a response body from the layout elements it has and sets it to the HTTP response
 */
class Layout extends Template
    //implements ResultInterface
{
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = array())
    {
        $this->setTemplate('Magento_Theme::root.phtml');
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @param ResponseInterface $response
     */
    public function renderResult(ResponseInterface $response)
    {
//        $this->layout->generateElements();
        $this->assign('headContent', $this->_layout->getBlock('head')->toHtml());
        $this->_layout->unsetElement('head');
        // todo: remove hardcoded value
        // $this->_translateInline->processResponseBody($output);
        $this->assign('layoutContent', $this->_layout->renderElement('root'));
        // todo: implement assign for variables: bodyClasses bodyAttributes
        $response->appendBody($this->toHtml());
    }
}
