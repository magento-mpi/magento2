<?php
/**
 * The list of available hooks
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Source;

class Hook
{
    /**
     * Path to environments section in the config
     */
    const XML_PATH_WEBHOOK = 'global/webhook/webhooks';

    /**
     * Cache of options
     *
     * @var null|array
     */
    protected $_options = null;

    /** @var  \Magento\Core\Model\Config */
    private $_config;

    /**
     * @param \Magento\Core\Model\Config $config
     */
    public function __construct(\Magento\Core\Model\Config $config )
    {
        $this->_config = $config;
    }

    /**
     * Get available topics
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();

            $configElement = $this->_config->getNode(self::XML_PATH_WEBHOOK);
            if ($configElement) {
                $this->_options = $configElement->asArray();
            }
        }

        return $this->_options;
    }

    /**
     * Scan config element to retrieve topics
     *
     * @return array
     */
    public function getTopicsForForm()
    {
        $elements = array();

        // process groups
        $elements = $this->_getTopicsForForm($this->toOptionArray(), array(), $elements);

        return $elements;
    }

    /**
     * Recursive helper function to dynamically build topic information for our form.
     * Seeks out nodes under 'webhook' stopping when it finds a leaf that contains 'label'
     * The value is constructed using the XML tree parents.
     * @param array $node
     * @param array $path
     * @param array $elements
     * @return array
     */
    protected function _getTopicsForForm($node, $path, $elements)
    {
        if (!empty($node['label'])) {
            $value = join('/', $path);

            $label = __($node['label']);

            $elements[] = array(
                'label' => $label,
                'value' => $value,
            );

            return $elements;
        }

        foreach ($node as $group => $child) {
            $path[] = $group;
            $elements = $this->_getTopicsForForm($child, $path, $elements);
            array_pop($path);
        }

        return $elements;
    }
}
