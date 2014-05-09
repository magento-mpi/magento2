<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Date
 */
namespace Magento\Framework\Data\Form\Element;

class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Form\ElementFactory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_elementFactory = $objectManager->create('Magento\Framework\Data\Form\ElementFactory');
        $this->_localeDate = $objectManager->get('\Magento\Framework\Stdlib\DateTime\Timezone');
    }

    /**
     * @dataProvider getValueDataProvider
     */
    public function testGetValue(array $data, $expect)
    {
        if (isset($data['date_format'])) {
            $data['date_format'] = $this->_localeDate->getDateFormat($data['date_format']);
        }
        if (isset($data['time_format'])) {
            $data['time_format'] = $this->_localeDate->getTimeFormat($data['time_format']);
        }
        /** @var $date \Magento\Framework\Data\Form\Element\Date*/
        $date = $this->_elementFactory->create('Magento\Framework\Data\Form\Element\Date', $data);
        $this->assertEquals($expect, $date->getValue());
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return array(
            array(
                array('date_format' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT,
                    'time_format' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT,
                    'value' => '1397832240'
                ),
                '4/18/14 2:44 PM'
            ),
            array(
                array('time_format' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT,
                    'value' => '1397832240'
                ),
                '2:44 PM'
            ),
            array(
                array('date_format' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT,
                    'value' => '1397832240'
                ),
                '4/18/14'
            )
        );
    }
}
