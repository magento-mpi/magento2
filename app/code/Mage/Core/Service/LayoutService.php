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
class Mage_Core_Service_LayoutService extends Mage_Core_Service_Type_Abstract
{
    /**
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * Constructor
     *
     * @param Mage_Core_Service_ObjectManager $serviceObjectManager
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     */
    public function __construct(
        Mage_Core_Service_ObjectManager $serviceObjectManager,
        Mage_Core_Service_Context $context,
        Mage_Core_Model_Layout_Factory $layoutFactory
    ) {
        parent::__construct($serviceObjectManager, $context);
        $this->_layoutFactory   = $layoutFactory;
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

    /**
     * @param Mage_Core_Model_Layout $layout
     * @param mixed $handle
     */
    public function loadLayout($layout, $handle = null)
    {
        if (null === $handle) {
            $handle = 'default';
        }

        if ($handle) {
            $this->addLayoutHandle($layout, $handle);
        }

        $this->loadLayoutUpdates($layout);

        $layout->setIsLoaded(true);
    }

    public function addLayoutHandle($layout, $handle)
    {
        if ($handle) {
            $layout->getUpdate()->addHandle($handle);
        }
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
    }

    public function generateLayout($layout)
    {
        $this->generateLayoutXml($layout);

        $this->generateLayoutBlocks($layout);

        $layout->setIsGenerated(true);
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
    }

    /**
     * @param null $layout
     * @param string $output
     * @return $string $output | true
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
}
