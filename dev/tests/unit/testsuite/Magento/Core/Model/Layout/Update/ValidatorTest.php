<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Layout\Update;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    public function setUp()
    {
        $this->_objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @param string $layoutUpdate
     * @param boolean $isSchemaValid
     * @return \Magento\Core\Model\Layout\Update\Validator
     */
    protected function _createValidator($layoutUpdate, $isSchemaValid = true)
    {
        $dirList = $this->getMockBuilder('Magento\Framework\App\Filesystem\DirectoryList')
            ->disableOriginalConstructor()
            ->getMock();
        $dirList->expects(
            $this->exactly(2)
        )->method(
            'getPath'
        )->will(
            $this->returnValue('dummyDir')
        );

        $domConfigFactory = $this->getMockBuilder(
            'Magento\Framework\Config\DomFactory'
        )->disableOriginalConstructor()->getMock();

        $params = array(
            'xml' => '<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . trim(
                $layoutUpdate
            ) . '</layout>',
            'schemaFile' => 'dummyDir/Magento/Framework/View/Layout/etc/page_layout.xsd'
        );

        $exceptionMessage = 'validation exception';
        $domConfigFactory->expects(
            $this->once()
        )->method(
            'createDom'
        )->with(
            $this->equalTo($params)
        )->will(
            $isSchemaValid ? $this->returnSelf() : $this->throwException(
                new \Magento\Framework\Config\Dom\ValidationException($exceptionMessage)
            )
        );

        $model = $this->_objectHelper->getObject(
            'Magento\Core\Model\Layout\Update\Validator',
            array('dirList' => $dirList, 'domConfigFactory' => $domConfigFactory)
        );

        return $model;
    }

    /**
     * @dataProvider testIsValidNotSecurityCheckDataProvider
     * @param string $layoutUpdate
     * @param boolean $isValid
     * @param boolean $expectedResult
     * @param array $messages
     */
    public function testIsValidNotSecurityCheck($layoutUpdate, $isValid, $expectedResult, $messages)
    {
        $model = $this->_createValidator($layoutUpdate, $isValid);
        $this->assertEquals(
            $expectedResult,
            $model->isValid(
                $layoutUpdate,
                \Magento\Core\Model\Layout\Update\Validator::LAYOUT_SCHEMA_PAGE_HANDLE,
                false
            )
        );
        $this->assertEquals($model->getMessages(), $messages);
    }

    /**
     * @return array
     */
    public function testIsValidNotSecurityCheckDataProvider()
    {
        return array(
            array('test', true, true, array()),
            array(
                'test',
                false,
                false,
                array(
                    \Magento\Core\Model\Layout\Update\Validator::XML_INVALID =>
                        'Please correct the XML data and try again. validation exception'
                )
            )
        );
    }

    /**
     * @dataProvider testIsValidSecurityCheckDataProvider
     * @param string $layoutUpdate
     * @param boolean $expectedResult
     * @param array $messages
     */
    public function testIsValidSecurityCheck($layoutUpdate, $expectedResult, $messages)
    {
        $model = $this->_createValidator($layoutUpdate);
        $this->assertEquals(
            $model->isValid(
                $layoutUpdate,
                \Magento\Core\Model\Layout\Update\Validator::LAYOUT_SCHEMA_PAGE_HANDLE,
                true
            ),
            $expectedResult
        );
        $this->assertEquals($model->getMessages(), $messages);
    }

    /**
     * @return array
     */
    public function testIsValidSecurityCheckDataProvider()
    {
        $insecureHelper = <<<XML
<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <handle id="handleId">
        <block class="Block_Class">
          <arguments>
              <argument name="test" xsi:type="helper" helper="Helper_Class"/>
          </arguments>
        </block>
    </handle>
</layout>
XML;
        $insecureUpdater = <<<XML
<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <handle id="handleId">
        <block class="Block_Class">
          <arguments>
              <argument name="test" xsi:type="string">
                  <updater>Updater_Model</updater>
                  <value>test</value>
              </argument>
          </arguments>
        </block>
    </handle>
</layout>
XML;
        $secureLayout = <<<XML
<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <handle id="handleId">
        <block class="Block_Class">
          <arguments>
              <argument name="test" xsi:type="string">test</argument>
          </arguments>
        </block>
    </handle>
</layout>
XML;
        return array(
            array(
                $insecureHelper,
                false,
                array(
                    \Magento\Core\Model\Layout\Update\Validator::HELPER_ARGUMENT_TYPE =>
                        'Helper arguments should not be used in custom layout updates.'
                )
            ),
            array(
                $insecureUpdater,
                false,
                array(
                    \Magento\Core\Model\Layout\Update\Validator::UPDATER_MODEL =>
                        'Updater model should not be used in custom layout updates.'
                )
            ),
            array($secureLayout, true, array())
        );
    }
}
