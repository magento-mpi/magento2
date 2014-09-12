<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Sendfriend\Block\Plugin\Catalog\Product;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sendfriend\Block\Plugin\Catalog\Product\View */
    protected $view;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Sendfriend\Model\Sendfriend|\PHPUnit_Framework_MockObject_MockObject */
    protected $sendfriendModel;

    /** @var \Magento\Catalog\Block\Product\View|\PHPUnit_Framework_MockObject_MockObject */
    protected $productView;


    protected function setUp()
    {
        $this->sendfriendModel = $this->getMock(
            'Magento\Sendfriend\Model\Sendfriend',
            array('__wakeup', 'canEmailToFriend'),
            array(),
            '',
            false
        );
        $this->productView = $this->getMock('Magento\Catalog\Block\Product\View', array(), array(), '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->view = $this->objectManagerHelper->getObject(
            'Magento\Sendfriend\Block\Plugin\Catalog\Product\View',
            [
                'sendfriend' => $this->sendfriendModel
            ]
        );

    }

    /**
     * @dataProvider afterCanEmailToFriendDataSet
     * @param bool $result
     * @param string $callSendfriend
     */
    public function testAfterCanEmailToFriend($result, $callSendfriend)
    {
        $this->sendfriendModel->expects($this->$callSendfriend())->method('canEmailToFriend')
            ->will($this->returnValue(true));

        $this->assertTrue($this->view->afterCanEmailToFriend($this->productView, $result));
    }

    public function afterCanEmailToFriendDataSet()
    {
        return array(
            array(true, 'never'),
            array(false, 'once')
        );
    }
}
