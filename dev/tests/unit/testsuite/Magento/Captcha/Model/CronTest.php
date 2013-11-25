<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Captcha\Model;

/**
 * Class \Magento\Captcha\Model\CronTest
 */
class CronTest extends \PHPUnit_Framework_TestCase
{
    /**
     * CAPTCHA helper
     *
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;

    /**
     * CAPTCHA helper
     *
     * @var \Magento\Captcha\Helper\Adminhtml\Data
     */
    protected $_adminHelper;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Captcha\Model\Resource\LogFactory
     */
    protected $_resLogFactory;

    /**
     * @var \Magento\Captcha\Model\Cron
     */
    protected $_model;

    /**
     * Create mocks and model
     */
    public function setUp()
    {
        $this->_helper = $this->getMock('Magento\Captcha\Helper\Data', array(), array(), '', false);
        $this->_adminHelper = $this->getMock('Magento\Captcha\Helper\Adminhtml\Data', array(), array(), '', false);
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $this->_resLogFactory = $this->getMock('Magento\Captcha\Model\Resource\LogFactory',
            array(), array(), '', false
        );

        $this->_model = new \Magento\Captcha\Model\Cron(
            $this->_resLogFactory,
            $this->_helper,
            $this->_adminHelper,
            $this->_filesystem,
            $this->_storeManager
        );
    }

    /**
     * @dataProvider getExpiredImages
     */
    public function testDeleteExpiredImages($websites, $isFile, $filename, $mTime, $timeout, $mustDelete)
    {
        $this->_storeManager->expects($this->once())->method('getWebsites')->will($this->returnValue($websites));
        foreach ($websites as $website) {
            $helper = $website->getIsDefault() ? $this->_adminHelper : $this->_helper;
            $helper->expects($this->once())
                ->method('getConfig')
                ->with('timeout', $website->getDefaultStore())
                ->will($this->returnValue($timeout));

            $this->_filesystem->expects($this->once())
                ->method('getNestedKeys')
                ->will($this->returnValue(array($filename)));
            $this->_filesystem->expects($this->once())->method('isFile')->will($this->returnValue($isFile));
            $this->_filesystem->expects($this->any())->method('getMTime')->will($this->returnValue($mTime));
            if ($mustDelete) {
                $this->_filesystem->expects($this->once())->method('delete')->with($filename);
            } else {
                $this->_filesystem->expects($this->never())->method('delete');
            }

            $this->_model->deleteExpiredImages();
        }
    }

    /**
     * @return array
     */
    public function getExpiredImages()
    {
        $defaultWebsite = new \Magento\Object(array('is_default' => true, 'default_store' => '1'));
        $website = new \Magento\Object(array('is_default' => false, 'default_store' => '1'));
        $time = time();
        return array(
            array(array($defaultWebsite), true, 'test.png', 50, ($time - 60) / 60, true),
            array(array($website), false, 'test.png', 50, ($time - 60) / 60, false),
            array(array($website), true, 'test.jpg', 50, ($time - 60) / 60, false),
            array(array($website), true, 'test.png', 50,  ($time - 20) / 60, false)
        );
    }
}
