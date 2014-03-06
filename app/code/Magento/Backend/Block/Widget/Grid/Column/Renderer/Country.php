<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country column renderer
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

use Magento\View\Element\AbstractBlock;

class Country
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Locale\ListsInterface
     */
    protected $localeLists;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Locale\ListsInterface $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Locale\ListsInterface $localeLists,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->localeLists = $localeLists;
    }

    /**
     * Render country grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $name = $this->localeLists->getCountryTranslation($data);
            if (empty($name)) {
                $name = $this->escapeHtml($data);
            }
            return $name;
        }
        return null;
    }
}
