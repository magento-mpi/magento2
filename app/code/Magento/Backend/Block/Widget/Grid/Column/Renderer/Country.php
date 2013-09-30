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
class Magento_Backend_Block_Widget_Grid_Column_Renderer_Country
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_LocaleInterface $locale,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($context, $data);
    }

    /**
     * Render country grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
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
