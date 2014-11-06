<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Layout;

class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test formatted time data
     */
    const TEST_FORMATTED_TIME = 'test_time';

    public function testBeforeSave()
    {
        $resourceModel = $this->getMock(
            'Magento\Core\Model\Resource\Layout\Update',
            array(
                '__wakeup',
                'formatDate',
                'getIdFieldName',
                'beginTransaction',
                'save',
                'addCommitCallback',
                'commit'
            ),
            array(),
            '',
            false
        );
        $dateTime = $this->getMock('\Magento\Framework\Stdlib\DateTime', array(), array());
        $dateTime->expects(
            $this->once()
        )->method(
            'formatDate'
        )->with(
            $this->isType('int')
        )->will(
            $this->returnValue(self::TEST_FORMATTED_TIME)
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var $model \Magento\Core\Model\Layout\Update */
        $model = $helper->getObject(
            'Magento\Core\Model\Layout\Update',
            array('resource' => $resourceModel, 'dateTime' => $dateTime)
        );
        $model->setId(0);
        // set any data to set _hasDataChanges flag
        $model->beforeSave();

        $this->assertEquals(self::TEST_FORMATTED_TIME, $model->getUpdatedAt());
    }
}
