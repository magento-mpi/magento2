<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_Modular_LayoutFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @var array
     */
    protected $_types;

    public function setUp()
    {
        $objectManager = Mage::getObjectManager();
        $this->_handlerFactory = $objectManager->get('Magento\Core\Model\Layout\Argument\HandlerFactory');
        $this->_types = $this->_handlerFactory->getTypes();
    }

    /**
     * @dataProvider layoutTypesDataProvider
     */
    public function testLayoutTypes($layout)
    {
        $layout = simplexml_load_file(
            $layout,
            'Magento\Core\Model\Layout\Element'
        );
        foreach ($layout->xpath('//*[@xsi:type]') as $argument) {
            $type = (string)$argument->attributes('xsi', true)->type;
            if (!in_array($type, $this->_types)) {
                continue;
            }
            try {
                /* @var $handler \Magento\Core\Model\Layout\Argument\HandlerInterface */
                $handler = $this->_handlerFactory->getArgumentHandlerByType($type);
                $argument = $handler->parse($argument);
                if ($this->_isIgnored($argument)) {
                    continue;
                }
                $handler->process($argument);
            } catch (InvalidArgumentException $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    /**
     * @return array
     */
    public function layoutTypesDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getLayoutFiles();
    }

    /**
     * @param $argument
     * @return bool
     */
    protected function _isIgnored($argument)
    {
        return
            // we can't process updaters without value
            !isset($argument['value']) && isset($argument['updaters'])

            // ignored objects
            || isset($argument['value']['object'])
                && in_array($argument['value']['object'], array(
                    'Magento\Catalog\Model\Resource\Product\Type\Grouped\AssociatedProductsCollection',
                    'Magento\Catalog\Model\Resource\Product\Collection\AssociatedProduct',
                    'Magento_Search_Model_Resource_Search_Grid_Collection',
                    'Magento\Wishlist\Model\Resource\Item\Collection\Grid',
                    'Magento_CustomerSegment_Model_Resource_Segment_Report_Detail_Collection',
                ))

            // ignored helpers
            || isset($argument['value']['helperClass']) &&
                in_array($argument['value']['helperClass'] . '::' . $argument['value']['helperMethod'], array(
                    'Magento\Pbridge\Helper\Data::getReviewButtonTemplate'
                ))

            // ignored options
            || isset($argument['value']['model'])
                && in_array($argument['value']['model'], array(
                    'Magento_Search_Model_Adminhtml_Search_Grid_Options',
                ));
    }
}
