<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Adminhtml\Rss\Grid;

/**
 * Class Link
 * @package Magento\Review\Block\Adminhtml\Grid\Rss
 */
class Link extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'rss/grid/link.phtml';

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface
     */
    protected $rssUrlBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder,
        array $data = array()
    ) {
        $this->rssUrlBuilder = $rssUrlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Set element Id
     */
    protected function _construct()
    {
        $this->setId('grid.rss.link');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->rssUrlBuilder->getUrl($this->getLinkParams());
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('Pending Reviews RSS');
    }

    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isRssAllowed()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getLinkParams()
    {
        return array('type' => 'review');
    }
}
