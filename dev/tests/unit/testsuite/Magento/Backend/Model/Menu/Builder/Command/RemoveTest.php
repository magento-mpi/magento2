<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Menu\Builder\Command;

class RemoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu\Builder\Command\Remove
     */
    protected $_model;

    protected $_params = array(
        'id' => 'item'
    );

    public function setUp()
    {
        $this->_model = new \Magento\Backend\Model\Menu\Builder\Command\Remove($this->_params);
    }

    public function testExecuteMarksItemAsRemoved()
    {
        $params = $this->_model->execute(array());
        $this->_params['removed'] = true;
        $this->assertEquals($this->_params, $params);
    }
}
