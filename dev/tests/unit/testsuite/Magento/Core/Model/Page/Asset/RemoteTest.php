<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Page\Asset;

class RemoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Page\Asset\Remote
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new \Magento\Core\Model\Page\Asset\Remote('https://127.0.0.1/magento/test/style.css', 'css');
    }

    public function testGetUrl()
    {
        $this->assertEquals('https://127.0.0.1/magento/test/style.css', $this->_object->getUrl());
    }

    public function testGetContentType()
    {
        $this->assertEquals('css', $this->_object->getContentType());
    }
}
