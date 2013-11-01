<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Items\Column;

/**
 * Sales Order items name column renderer
 */
class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * Core string
     *
     * @var \Magento\Filter\FilterManager
     */
    protected $filter;

    /**
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Filter\FilterManager $filter
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Filter\FilterManager $filter,
        array $data = array()
    ) {
        $this->filter = $filter;
        parent::__construct($optionFactory, $coreData, $context, $data);
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filter->truncate($value, array(
            'length' => $length,
            'etc' => $etc,
            'remainder' => $remainder,
            'breakWords' => $breakWords
        ));
    }

    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $remainder = '';
        $value = $this->truncateString($value, 55, '', $remainder);
        $result = array(
            'value' => nl2br($value),
            'remainder' => nl2br($remainder)
        );

        return $result;
    }
}
