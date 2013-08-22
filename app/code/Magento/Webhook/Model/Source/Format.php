<?php
/**
 * The list of available formats
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Source_Format
{
    /** @var string[] $_formats */
    private $_formats;

    /**
     * Cache of options
     *
     * @var null|array
     */
    protected $_options = null;

    /**
     * @param string[] $formats
     */
    public function __construct(array $formats)
    {
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
                'label' => __($format),
                'value' => $formatName,
            );
        }

        return $elements;
    }
}
