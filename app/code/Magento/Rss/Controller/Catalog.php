<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller;

/**
 * RSS Controller for Catalog feeds
 */
class Catalog extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Store\Config $storeConfig
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Store\Config $storeConfig
    ) {
        $this->_storeConfig = $storeConfig;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function newAction()
    {
        $this->_genericAction('new');
    }

    /**
     * @return void
     */
    public function specialAction()
    {
        $this->_genericAction('special');
    }

    /**
     * @return void
     */
    public function salesruleAction()
    {
        $this->_genericAction('salesrule');
    }

    /**
     * @return void
     */
    public function categoryAction()
    {
         $this->_genericAction('category');
    }

    /**
     * Render or forward to "no route" action if this type of RSS is disabled
     *
     * @param string $code
     * @return void
     */
    protected function _genericAction($code)
    {
        if ($this->_isEnabled($code)) {
            $this->_render();
        } else {
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Whether specified type of RSS is enabled
     *
     * @param string $code
     * @return bool
     */
    protected function _isEnabled($code)
    {
        return $this->_storeConfig->getConfigFlag("rss/catalog/{$code}");
    }

    /**
     * Render as XML-document using layout handle without inheriting any other handles
     *
     * @return void
     */
    protected function _render()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
