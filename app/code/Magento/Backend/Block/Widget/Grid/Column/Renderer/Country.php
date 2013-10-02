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

class Country
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($context, $data);
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
            $name = $this->_locale->getCountryTranslation($data);
            if (empty($name)) {
                $name = $this->escapeHtml($data);
            }
            return $name;
        }
        return null;
    }
}
