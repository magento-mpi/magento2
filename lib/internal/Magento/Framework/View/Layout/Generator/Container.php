<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Generator;

use Magento\Framework\View\Layout;

class Container implements Layout\GeneratorInterface
{
    /**#@+
     * Names of container options in layout
     */
    const CONTAINER_OPT_HTML_TAG = 'htmlTag';
    const CONTAINER_OPT_HTML_CLASS = 'htmlClass';
    const CONTAINER_OPT_HTML_ID = 'htmlId';
    const CONTAINER_OPT_LABEL = 'label';
    /**#@-*/

    const TYPE = 'container';

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * @param \Magento\Framework\View\Layout\Reader\Context $readerContext
     * @param string $elementName
     * @param null $layout
     * @return void
     */
    public function process(Layout\Reader\Context $readerContext, $elementName, $layout = null)
    {
        $this->_generateContainer(
            $readerContext->getScheduledStructure(),
            $readerContext->getStructure(),
            $elementName
        );
    }

    /**
     * Set container-specific data to structure element
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\Data\Structure $structure
     * @param $elementName
     * @throws \Magento\Framework\Exception If any of arguments are invalid
     * @return void
     */
    protected function _generateContainer(
        Layout\ScheduledStructure $scheduledStructure,
        \Magento\Framework\Data\Structure $structure,
        $elementName
    ) {
        list(, $data) = $scheduledStructure->getElement($elementName);
        $options = $data['attributes'];
        $structure->setAttribute($elementName, Layout\Element::CONTAINER_OPT_LABEL, $options[Layout\Element::CONTAINER_OPT_LABEL]);
        unset($options[Layout\Element::CONTAINER_OPT_LABEL]);
        unset($options['type']);
        $allowedTags = array(
            'dd',
            'div',
            'dl',
            'fieldset',
            'header',
            'footer',
            'hgroup',
            'ol',
            'p',
            'section',
            'table',
            'tfoot',
            'ul'
        );
        if (!empty($options[Layout\Element::CONTAINER_OPT_HTML_TAG]) && !in_array(
                $options[Layout\Element::CONTAINER_OPT_HTML_TAG],
                $allowedTags
            )
        ) {
            throw new \Magento\Framework\Exception(
                __(
                    'Html tag "%1" is forbidden for usage in containers. Consider to use one of the allowed: %2.',
                    $options[Layout\Element::CONTAINER_OPT_HTML_TAG],
                    implode(', ', $allowedTags)
                )
            );
        }
        if (empty($options[Layout\Element::CONTAINER_OPT_HTML_TAG])
            && (!empty($options[Layout\Element::CONTAINER_OPT_HTML_ID])
                || !empty($options[Layout\Element::CONTAINER_OPT_HTML_CLASS]))
        ) {
            throw new \Magento\Framework\Exception(
                'HTML ID or class will not have effect, if HTML tag is not specified.'
            );
        }
        foreach ($options as $key => $value) {
            $structure->setAttribute($elementName, $key, $value);
        }
    }
}