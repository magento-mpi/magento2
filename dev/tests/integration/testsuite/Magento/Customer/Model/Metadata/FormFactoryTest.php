<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

use Magento\TestFramework\Helper\Bootstrap;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    private $_requestData;

    /** @var array */
    private $_expectedData;

    public function setUp()
    {
        $this->_requestData = [
            'id' => 13,
            'default_shipping' => true,
            'default_billing' => false,
            'company' => 'eBay Inc.',
            'fax' => '(444) 444-4444',
            'middlename' => 'MiddleName',
            'prefix' => 'Mr.',
            'suffix' => 'Esq.',
            'vat_id' => 'S46',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => ['2211 North First Street'],
            'city' => 'San Jose',
            'country_id' => 'US',
            'postcode' => '95131',
            'telephone' => '5135135135',
            'region_id' => 12,
            'region' => 'California'
        ];

        $this->_expectedData = $this->_requestData;
        $this->_expectedData['street'] = trim(implode("\n", $this->_expectedData['street']));

        unset($this->_expectedData['id']);
        unset($this->_expectedData['default_shipping']);
        unset($this->_expectedData['default_billing']);
        unset($this->_expectedData['middlename']);
        unset($this->_expectedData['prefix']);
        unset($this->_expectedData['suffix']);
    }

    public function testCreate()
    {
        /** @var FormFactory $formFactory */
        $formFactory = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Metadata\FormFactory');
        $form = $formFactory->create('customer_address', 'customer_address_edit');

        $this->assertInstanceOf('\Magento\Customer\Model\Metadata\Form', $form);
        $this->assertNotEmpty($form->getAttributes());

        /** @var \Magento\App\RequestInterface $request */
        $request = Bootstrap::getObjectManager()->get('Magento\App\RequestInterface');
        $request->setParams($this->_requestData);

        $this->assertEquals($this->_expectedData, $form->restoreData($form->extractData($request)));
    }
}
