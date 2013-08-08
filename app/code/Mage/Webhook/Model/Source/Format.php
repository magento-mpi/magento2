<?php
/**
 * The list of available formats
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Source_Format
{
    /** @var Magento_Core_Model_Translate  */
    private $_translator;

    /** @var string[] $_formats */
    private $_formats;

    /**
     * Cache of options
     *
     * @var null|array
     */
    protected $_options = null;

    /**
     * @param Magento_Core_Model_Translate $translator
     * @param string[] $formats
     */
    public function __construct(array $formats, Magento_Core_Model_Translate $translator)
    {
        $this->_translator = $translator;
        $this->_formats = $formats;
    }

    /**
     * Get available formats
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        return $this->_formats;
    }

    /**
     * Return non-empty formats for use by a form
     *
     * @return array
     */
    public function getFormatsForForm()
    {
        $elements = array();
        foreach ($this->_formats as $formatName => $format) {
            $elements[] = array(
                'label' => $this->_translator->translate(array($format)),
                'value' => $formatName,
            );
        }

        return $elements;
    }
}
