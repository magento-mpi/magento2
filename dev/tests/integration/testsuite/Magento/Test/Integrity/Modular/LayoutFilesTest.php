<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
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

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_handlerFactory = $objectManager->get('Magento_Core_Model_Layout_Argument_HandlerFactory');
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
                    'Magento\Search\Model\Resource\Search\Grid\Collection',
                    'Magento\Wishlist\Model\Resource\Item\Collection\Grid',
                    'Magento\CustomerSegment\Model\Resource\Segment\Report\Detail\Collection',
                ))

            // ignored helpers
            || isset($argument['value']['helperClass']) &&
                in_array($argument['value']['helperClass'] . '::' . $argument['value']['helperMethod'], array(
                    'Magento\Pbridge\Helper\Data::getReviewButtonTemplate'
                ))

            // ignored options
            || isset($argument['value']['model'])
                && in_array($argument['value']['model'], array(
                    'Magento\Search\Model\Adminhtml\Search\Grid\Options',
                ));
    }
}
