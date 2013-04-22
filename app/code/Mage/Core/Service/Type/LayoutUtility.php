<?php
/**
 * Layout Utility Service.
 *
 * Purpose: serve infrastructural functionality.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Type_LayoutUtility extends Mage_Core_Service_Type_Abstract
{
    /**
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * Constructor
     *
     * @param Mage_Core_Service_Manager $manager
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param string $areaCode
     */
    public function __construct(
        Mage_Core_Service_Manager $manager,
        Mage_Core_Service_Context $context,
        Mage_Core_Model_Layout_Factory $layoutFactory
    ) {
        parent::__construct($manager, $context);
        $this->_layoutFactory   = $layoutFactory;
    }

    public function addLayoutHandles($layout, $handles)
    {
        $layout->getUpdate()->addHandle($handles);
    }

    /**
     * Add layout updates handles associated with the action page
     *
     * @param Mage_Core_Model_Layout $layout
     * @param string $handle
     * @param array $parameters page parameters
     * @return bool
     */
    public function addPageLayoutHandles($layout, $handle, array $parameters = array())
    {
        $pageHandles = array($handle);
        foreach ($parameters as $key => $value) {
            $pageHandles[] = $handle . '_' . $key . '_' . $value;
        }
        return $layout->getUpdate()->addPageHandles(array_reverse($pageHandles));
    }

    /**
     * @param Mage_Core_Model_Layout $layout
     * @param mixed $handles
     */
    public function loadLayout($layout, $handles = null)
    {
        // if handles were specified in arguments load them first
        if (false !== $handles && '' !== $handles) {
            $layout->getUpdate()->addHandle($handles ? $handles : 'default');
        }

        $this->loadLayoutUpdates($layout);

        $layout->setIsLoaded(true);
    }

    public function generateLayout($layout)
    {
        $this->generateLayoutXml($layout);

        $this->generateLayoutBlocks($layout);

        $layout->setIsGenerated(true);
    }

    public function loadLayoutUpdates($layout)
    {
        Mage::dispatchEvent(
            'controller_action_layout_load_before',
            array('layout' => $layout)
        );

        Magento_Profiler::start('layout_load');
        $layout->getUpdate()->load();
        Magento_Profiler::stop('layout_load');

        return $this;
    }

    public function generateLayoutXml($layout = null)
    {
        Mage::dispatchEvent(
            'controller_action_layout_generate_xml_before',
            array('layout' => $layout)
        );

        Magento_Profiler::start('layout_generate_xml');
        $layout->generateXml();
        Magento_Profiler::stop('layout_generate_xml');

        Mage::dispatchEvent(
            'controller_action_layout_generate_xml_after',
            array('layout' => $layout)
        );

        return $this;
    }

    public function generateLayoutBlocks($layout = null)
    {
        // dispatch event for adding xml layout elements
        Mage::dispatchEvent(
            'controller_action_layout_generate_blocks_before',
            array('layout' => $layout)
        );

        Magento_Profiler::start('layout_generate_blocks');
        $layout->generateElements();
        Magento_Profiler::stop('layout_generate_blocks');

        Mage::dispatchEvent(
            'controller_action_layout_generate_blocks_after',
            array('layout' => $layout)
        );

        return $this;
    }

    /**
     * @param null $layout
     * @param string $output
     * @return $this
     */
    public function renderLayout($layout = null, $output = '')
    {
        if ('' !== $output) {
            $layout->addOutputElement($output);
        }

        if (!$layout->isDirectOutput()) {
            $output = $layout->getOutput();
            Mage::getSingleton('Mage_Core_Model_Translate_Inline')->processResponseBody($output);
            return $output;
        }

        return true;
    }

    protected function _renderTitles($layout, $titles)
    {
        if ($titles) {
            $titleBlock = $layout->getBlock('head');
            if ($titleBlock) {
                $title = trim($titleBlock->getTitle());
                if ($title) {
                    array_unshift($titles, $title);
                }

                $titleBlock->setTitle(array_reverse($titles));
            }
        }
    }

    /**
     * Retrieve layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout($area)
    {
        return $this->_layoutFactory->createLayout(array('area' => $area));
    }
}
