<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate grid renderer
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Reward\Rate\Grid\Column\Renderer;

class Rate
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Reward\Model\Reward\Rate
     */
    protected $_rate;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\Reward\Rate $rate
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\Reward\Rate $rate,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_rate = $rate;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $websiteId = $row->getWebsiteId();
        return $this->_rate->getRateText(
            $row->getDirection(),
            $row->getPoints(),
            $row->getCurrencyAmount(),
            0 == $websiteId ? null : $this->_storeManager->getWebsite($websiteId)->getBaseCurrencyCode()
        );
    }
}
