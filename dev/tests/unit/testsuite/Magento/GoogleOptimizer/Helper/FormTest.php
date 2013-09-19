<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Helper;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GoogleOptimizer\Helper\Form
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldsetMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_experimentCodeMock;

    public function setUp()
    {
        $this->_formMock = $this->getMock('Magento\Data\Form', array('setFieldNameSuffix', 'addFieldset'), array(), '',
            false);
        $this->_fieldsetMock = $this->getMock('Magento\Data\Form\Element\Fieldset', array(), array(), '', false);
        $this->_experimentCodeMock = $this->getMock('Magento\GoogleOptimizer\Model\Code',
            array('getExperimentScript', 'getCodeId'), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject('Magento\GoogleOptimizer\Helper\Form');
    }

    public function testAddFieldsWithExperimentCode()
    {
        $experimentCode = 'some-code';
        $experimentCodeId = 'code-id';
        $this->_experimentCodeMock->expects($this->once())->method('getExperimentScript')
            ->will($this->returnValue($experimentCode));
        $this->_experimentCodeMock->expects($this->once())->method('getCodeId')
            ->will($this->returnValue($experimentCodeId));
        $this->_prepareFormMock($experimentCode, $experimentCodeId);

        $this->_helper->addGoogleoptimizerFields($this->_formMock, $this->_experimentCodeMock);
    }

    public function testAddFieldsWithoutExperimentCode()
    {
        $experimentCode = array();
        $experimentCodeId = '';
        $this->_prepareFormMock($experimentCode, $experimentCodeId);

        $this->_helper->addGoogleoptimizerFields($this->_formMock, null);
    }

    /**
     * @param string|array $experimentCode
     * @param string $experimentCodeId
     */
    protected function _prepareFormMock($experimentCode, $experimentCodeId)
    {
        $this->_formMock->expects($this->once())->method('addFieldset')
            ->with('googleoptimizer_fields', array('legend' => 'Google Analytics Content Experiments Code'))
            ->will($this->returnValue($this->_fieldsetMock));

        $this->_fieldsetMock->expects($this->at(0))->method('addField')
            ->with('experiment_script', 'textarea', array(
                'name' => 'experiment_script',
                'label' => 'Experiment Code',
                'value' => $experimentCode,
                'class' => 'textarea googleoptimizer',
                'required' => false,
                'note' => 'Note: Experiment code should be added to the original page only.',
            ));

        $this->_fieldsetMock->expects($this->at(1))->method('addField')
            ->with('code_id', 'hidden', array(
                'name' => 'code_id',
                'value' => $experimentCodeId,
                'required' => false,
            ));
        $this->_formMock->expects($this->once())->method('setFieldNameSuffix')->with('google_experiment');
    }
}
