<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Block\Bml;

use Magento\Framework\View\Element\Template;
use Magento\Paypal\Model\Config;

class Banners extends Template
{
    /**
     * @var string
     */
    protected $_section;

    /**
     * @var int
     */
    protected $_position;

    /**
     * @var \Magento\Paypal\Model\Config
     */
    protected $_paypalConfig;

    /**
     * @param Template\Context $context
     * @param Config $paypalConfig
     * @param string $section
     * @param int $position
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $paypalConfig,
        $section = '',
        $position = 0,
        array $data = array()
    ) {
        $this->_section = (string)$section;
        $this->_position = (int)$position;
        $this->_paypalConfig = $paypalConfig;
        parent::__construct($context, $data);
    }

    /**
     * Disable block output if banner turned off or PublisherId is miss
     *
     * @return string
     */
    protected function _toHtml()
    {
        $publisherId = $this->_paypalConfig->getBmlPublisherId();
        $display = $this->_paypalConfig->getBmlDisplay($this->_section);
        $position = $this->_paypalConfig->getBmlPosition($this->_section);
        if (!$publisherId || $display == 0 || $this->_position != $position) {
            return '';
        }
        $this->setData('publisher_id', $publisherId);
        $this->setData('size', $this->_paypalConfig->getBmlSize($this->_section));
        return parent::_toHtml();
    }
}
