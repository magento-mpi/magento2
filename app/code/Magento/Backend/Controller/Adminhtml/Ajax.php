<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 */
namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Ajax extends Action
{
    /**
     * @var \Magento\Framework\Translate\Inline\ParserInterface
     */
    protected $inlineParser;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Translate\Inline\ParserInterface $inlineParser
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Translate\Inline\ParserInterface $inlineParser
    ) {
        parent::__construct($context);

        $this->inlineParser = $inlineParser;
    }

    /**
     * Ajax action for inline translation
     *
     * @return void
     */
    public function translateAction()
    {
        $translate = (array)$this->getRequest()->getPost('translate');

        try {
            $this->inlineParser->processAjaxPost($translate);
            $response = "{success:true}";
        } catch (\Exception $e) {
            $response = "{error:true,message:'" . $e->getMessage() . "'}";
        }
        $this->getResponse()->setBody($response);

        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
