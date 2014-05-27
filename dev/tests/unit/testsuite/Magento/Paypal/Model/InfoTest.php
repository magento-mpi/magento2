<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Paypal\Model\Info */
    protected $info;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->info = $this->objectManagerHelper->getObject(
            'Magento\Paypal\Model\Info'
        );
    }

    /**
     * @dataProvider additionalInfoDataProvider
     * @param array $additionalInfo
     * @param array $expectation
     */
    public function testGetPaymentInfo($additionalInfo, $expectation)
    {
        /** @var \Magento\Payment\Model\Info $paymentInfo */
        $paymentInfo = $this->objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $paymentInfo->setAdditionalInformation($additionalInfo);
        $this->assertEquals($expectation, $this->info->getPaymentInfo($paymentInfo));
    }

    /**
     * @dataProvider additionalInfoDataProvider
     * @param array $additionalInfo
     * @param array $expectation
     */
    public function testGetPaymentInfoLabelValues($additionalInfo, $expectation)
    {
        /** @var \Magento\Payment\Model\Info $paymentInfo */
        $paymentInfo = $this->objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $paymentInfo->setAdditionalInformation($additionalInfo);
        $this->assertEquals(
            $this->_prepareLabelValuesExpectation($expectation),
            $this->info->getPaymentInfo($paymentInfo, true)
        );
    }

    /**
     * @dataProvider additionalInfoPublicDataProvider
     * @param array $additionalInfo
     * @param array $expectation
     */
    public function testGetPublicPaymentInfo($additionalInfo, $expectation)
    {
        /** @var \Magento\Payment\Model\Info $paymentInfo */
        $paymentInfo = $this->objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $paymentInfo->setAdditionalInformation($additionalInfo);
        $this->assertEquals(
            $this->_prepareLabelValuesExpectation($expectation),
            $this->info->getPublicPaymentInfo($paymentInfo, true)
        );
    }

    /**
     * @dataProvider additionalInfoPublicDataProvider
     * @param array $additionalInfo
     * @param array $expectation
     */
    public function testGetPublicPaymentInfoLabelValues($additionalInfo, $expectation)
    {
        /** @var \Magento\Payment\Model\Info $paymentInfo */
        $paymentInfo = $this->objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $paymentInfo->setAdditionalInformation($additionalInfo);
        $this->assertEquals($expectation, $this->info->getPublicPaymentInfo($paymentInfo));
    }

    /**
     * @dataProvider importToPaymentDataProvider
     * @param array $mapping
     * @param array $expectation
     */
    public function testImportToPayment($mapping, $expectation)
    {
        // we create $from object, based on mapping
        $from = new \Magento\Framework\Object($mapping);
        /** @var \Magento\Payment\Model\Info $paymentInfo */
        $paymentInfo = $this->objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $this->info->importToPayment($from, $paymentInfo);
        $this->assertEquals($expectation, $paymentInfo->getAdditionalInformation());
    }

    /**
     * @dataProvider importToPaymentDataProvider
     * @param array $mapping
     * @param array $expectation
     */
    public function testExportFromPayment($mapping, $expectation)
    {
        /** @var \Magento\Payment\Model\Info $paymentInfo */
        $paymentInfo = $this->objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $paymentInfo->setAdditionalInformation($expectation);

        // we create $to empty object
        $to = new \Magento\Framework\Object();
        $this->info->exportFromPayment($paymentInfo, $to);
        $this->assertEquals($mapping, $to->getData());
    }

    /**
     * @dataProvider importToPaymentDataProvider
     * @param array $mapping
     * @param array $expectation
     */
    public function testExportFromPaymentCustomMapping($mapping, $expectation)
    {
        /** @var \Magento\Payment\Model\Info $paymentInfo */
        $paymentInfo = $this->objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $paymentInfo->setAdditionalInformation($expectation);

        // we create $to empty object
        $to = new \Magento\Framework\Object();
        $this->info->exportFromPayment($paymentInfo, $to, array_flip($mapping));
        $this->assertEquals($mapping, $to->getData());
    }

    /**
     * Converts expectation result from ['key' => ['label' => 'Label', 'value' => 'Value']] to ['Label' => 'Value']
     *
     * @param $expectation
     * @return array
     */
    private function _prepareLabelValuesExpectation($expectation)
    {
        $labelValueExpectation = [];
        foreach ($expectation as $data) {
            $labelValueExpectation[$data['label']] = $data['value'];
        }
        return $labelValueExpectation;
    }

    /**
     * List of Labels
     *
     * @return array
     */
    public function additionalInfoDataProvider()
    {
        return [
            [
                [
                    Info::PAYPAL_PAYER_ID => Info::PAYPAL_PAYER_ID,
                    Info::PAYPAL_PAYER_EMAIL => Info::PAYPAL_PAYER_EMAIL,
                    Info::PAYPAL_PAYER_STATUS => Info::PAYPAL_PAYER_STATUS,
                    Info::PAYPAL_ADDRESS_ID => Info::PAYPAL_ADDRESS_ID,
                    Info::PAYPAL_ADDRESS_STATUS => Info::PAYPAL_ADDRESS_STATUS,
                    Info::PAYPAL_PROTECTION_ELIGIBILITY => Info::PAYPAL_PROTECTION_ELIGIBILITY,
                    Info::PAYPAL_FRAUD_FILTERS => Info::PAYPAL_FRAUD_FILTERS,
                    Info::PAYPAL_CORRELATION_ID => Info::PAYPAL_CORRELATION_ID,
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::PAYPAL_AVS_CODE => 'A',
                    Info::PAYPAL_CVV2_MATCH => 'M',
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE_CNPJ,
                    Info::CENTINEL_VPAS => '2',
                    Info::CENTINEL_ECI => '01'
                ],
                [
                    Info::PAYPAL_PAYER_ID => [
                        'label' => 'Payer ID',
                        'value' => Info::PAYPAL_PAYER_ID,
                    ],
                    Info::PAYPAL_PAYER_EMAIL => [
                        'label' => 'Payer Email',
                        'value' => Info::PAYPAL_PAYER_EMAIL,
                     ],
                    Info::PAYPAL_PAYER_STATUS => [
                        'label' => 'Payer Status',
                        'value' => Info::PAYPAL_PAYER_STATUS,
                    ],
                    Info::PAYPAL_ADDRESS_ID => [
                        'label' => 'Payer Address ID',
                        'value' => Info::PAYPAL_ADDRESS_ID,
                    ],
                    Info::PAYPAL_ADDRESS_STATUS => [
                        'label' => 'Payer Address Status',
                        'value' => Info::PAYPAL_ADDRESS_STATUS,
                    ],
                    Info::PAYPAL_PROTECTION_ELIGIBILITY => [
                        'label' => 'Merchant Protection Eligibility',
                        'value' => Info::PAYPAL_PROTECTION_ELIGIBILITY,
                    ],
                    Info::PAYPAL_FRAUD_FILTERS => [
                        'label' => 'Triggered Fraud Filters',
                        'value' => Info::PAYPAL_FRAUD_FILTERS,
                    ],
                    Info::PAYPAL_CORRELATION_ID => [
                        'label' => 'Last Correlation ID',
                        'value' => Info::PAYPAL_CORRELATION_ID,
                    ],
                    Info::PAYPAL_AVS_CODE => [
                        'label' => 'Address Verification System Response',
                        'value' => '#A: Matched Address only (no ZIP)',
                    ],
                    Info::PAYPAL_CVV2_MATCH => [
                        'label' => 'CVV2 Check Result by PayPal',
                        'value' => '#M: Matched (CVV2CSC)',
                    ],
                    Info::CENTINEL_VPAS => [
                        'label' => 'PayPal/Centinel Visa Payer Authentication Service Result',
                        'value' => '#2: Authenticated, Good Result',
                    ],
                    Info::CENTINEL_ECI => [
                        'label' => 'PayPal/Centinel Electronic Commerce Indicator',
                        'value' => '#01: Merchant Liability',
                    ],
                    Info::BUYER_TAX_ID => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => Info::BUYER_TAX_ID,
                    ],
                    Info::BUYER_TAX_ID_TYPE => [
                        'label' => 'Buyer\'s Tax ID Type',
                        'value' => 'CNPJ',
                    ],
                    'last_trans_id' => [
                        'label' => 'Last Transaction ID',
                        'value' => NULL
                    ]
                ]
            ],
            [
                [
                    Info::PAYPAL_PAYER_ID => Info::PAYPAL_PAYER_ID,
                    Info::PAYPAL_PAYER_EMAIL => Info::PAYPAL_PAYER_EMAIL,
                    Info::PAYPAL_PAYER_STATUS => Info::PAYPAL_PAYER_STATUS,
                    Info::PAYPAL_ADDRESS_ID => Info::PAYPAL_ADDRESS_ID,
                    Info::PAYPAL_ADDRESS_STATUS => Info::PAYPAL_ADDRESS_STATUS,
                    Info::PAYPAL_PROTECTION_ELIGIBILITY => Info::PAYPAL_PROTECTION_ELIGIBILITY,
                    Info::PAYPAL_FRAUD_FILTERS => Info::PAYPAL_FRAUD_FILTERS,
                    Info::PAYPAL_CORRELATION_ID => Info::PAYPAL_CORRELATION_ID,
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::PAYPAL_AVS_CODE => Info::PAYPAL_AVS_CODE,
                    Info::PAYPAL_CVV2_MATCH => Info::PAYPAL_CVV2_MATCH,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE,
                    Info::CENTINEL_VPAS => Info::CENTINEL_VPAS,
                    Info::CENTINEL_ECI => Info::CENTINEL_ECI
                ],
                [
                    Info::PAYPAL_PAYER_ID => [
                        'label' => 'Payer ID',
                        'value' => Info::PAYPAL_PAYER_ID,
                    ],
                    Info::PAYPAL_PAYER_EMAIL => [
                        'label' => 'Payer Email',
                        'value' => Info::PAYPAL_PAYER_EMAIL,
                    ],
                    Info::PAYPAL_PAYER_STATUS => [
                        'label' => 'Payer Status',
                        'value' => Info::PAYPAL_PAYER_STATUS,
                    ],
                    Info::PAYPAL_ADDRESS_ID => [
                        'label' => 'Payer Address ID',
                        'value' => Info::PAYPAL_ADDRESS_ID,
                    ],
                    Info::PAYPAL_ADDRESS_STATUS => [
                        'label' => 'Payer Address Status',
                        'value' => Info::PAYPAL_ADDRESS_STATUS,
                    ],
                    Info::PAYPAL_PROTECTION_ELIGIBILITY => [
                        'label' => 'Merchant Protection Eligibility',
                        'value' => Info::PAYPAL_PROTECTION_ELIGIBILITY,
                    ],
                    Info::PAYPAL_FRAUD_FILTERS => [
                        'label' => 'Triggered Fraud Filters',
                        'value' => Info::PAYPAL_FRAUD_FILTERS,
                    ],
                    Info::PAYPAL_CORRELATION_ID => [
                        'label' => 'Last Correlation ID',
                        'value' => Info::PAYPAL_CORRELATION_ID,
                    ],
                    Info::PAYPAL_AVS_CODE => [
                        'label' => 'Address Verification System Response',
                        'value' => '#paypal_avs_code',
                    ],
                    Info::PAYPAL_CVV2_MATCH => [
                        'label' => 'CVV2 Check Result by PayPal',
                        'value' => '#paypal_cvv2_match',
                    ],
                    Info::CENTINEL_VPAS => [
                        'label' => 'PayPal/Centinel Visa Payer Authentication Service Result',
                        'value' => '#centinel_vpas_result',
                    ],
                    Info::CENTINEL_ECI => [
                        'label' => 'PayPal/Centinel Electronic Commerce Indicator',
                        'value' => '#centinel_eci_result',
                    ],
                    Info::BUYER_TAX_ID => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => Info::BUYER_TAX_ID,
                    ],
                    'last_trans_id' => [
                        'label' => 'Last Transaction ID',
                        'value' => NULL
                    ]
                ]
            ]
        ];
    }

    /**
     *List of public labels
     *
     * @return array
     */
    public function additionalInfoPublicDataProvider()
    {
        return [
            [
                [
                    Info::PAYPAL_PAYER_EMAIL => Info::PAYPAL_PAYER_EMAIL,
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE_CNPJ
                ],
                [
                    Info::PAYPAL_PAYER_EMAIL => [
                        'label' => 'Payer Email',
                        'value' => Info::PAYPAL_PAYER_EMAIL,
                    ],
                    Info::BUYER_TAX_ID => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => Info::BUYER_TAX_ID,
                    ],
                    Info::BUYER_TAX_ID_TYPE => [
                        'label' => 'Buyer\'s Tax ID Type',
                        'value' => 'CNPJ',
                    ]
                ]
            ],
            [
                [
                    Info::PAYPAL_PAYER_EMAIL => Info::PAYPAL_PAYER_EMAIL,
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE
                ],
                [
                    Info::PAYPAL_PAYER_EMAIL => [
                        'label' => 'Payer Email',
                        'value' => Info::PAYPAL_PAYER_EMAIL,
                    ],
                    Info::BUYER_TAX_ID => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => Info::BUYER_TAX_ID,
                    ]
                ]
            ]
        ];
    }

    /**
     * Mapping and expectation
     *
     * @return array
     */
    public function importToPaymentDataProvider()
    {
        return [
            [
                [
                    Info::PAYER_ID => Info::PAYPAL_PAYER_ID,
                    Info::PAYER_EMAIL => Info::PAYPAL_PAYER_EMAIL,
                    Info::PAYER_STATUS => Info::PAYPAL_PAYER_STATUS,
                    Info::ADDRESS_ID => Info::PAYPAL_ADDRESS_ID,
                    Info::ADDRESS_STATUS => Info::PAYPAL_ADDRESS_STATUS,
                    Info::PROTECTION_EL => Info::PAYPAL_PROTECTION_ELIGIBILITY,
                    Info::FRAUD_FILTERS => Info::PAYPAL_FRAUD_FILTERS,
                    Info::CORRELATION_ID => Info::PAYPAL_CORRELATION_ID,
                    Info::AVS_CODE => Info::PAYPAL_AVS_CODE,
                    Info::CVV2_MATCH => Info::PAYPAL_CVV2_MATCH,
                    Info::CENTINEL_VPAS => Info::CENTINEL_VPAS,
                    Info::CENTINEL_ECI => Info::CENTINEL_ECI,
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE,
                    Info::PAYMENT_STATUS => Info::PAYMENT_STATUS_GLOBAL,
                    Info::PENDING_REASON => Info::PENDING_REASON_GLOBAL,
                    Info::IS_FRAUD => Info::IS_FRAUD_GLOBAL
                ],
                [
                    Info::PAYPAL_PAYER_ID => Info::PAYPAL_PAYER_ID,
                    Info::PAYPAL_PAYER_EMAIL => Info::PAYPAL_PAYER_EMAIL,
                    Info::PAYPAL_PAYER_STATUS => Info::PAYPAL_PAYER_STATUS,
                    Info::PAYPAL_ADDRESS_ID => Info::PAYPAL_ADDRESS_ID,
                    Info::PAYPAL_ADDRESS_STATUS => Info::PAYPAL_ADDRESS_STATUS,
                    Info::PAYPAL_PROTECTION_ELIGIBILITY => Info::PAYPAL_PROTECTION_ELIGIBILITY,
                    Info::PAYPAL_FRAUD_FILTERS => Info::PAYPAL_FRAUD_FILTERS,
                    Info::PAYPAL_CORRELATION_ID => Info::PAYPAL_CORRELATION_ID,
                    Info::PAYPAL_AVS_CODE => Info::PAYPAL_AVS_CODE,
                    Info::PAYPAL_CVV2_MATCH => Info::PAYPAL_CVV2_MATCH,
                    Info::CENTINEL_VPAS => Info::CENTINEL_VPAS,
                    Info::CENTINEL_ECI => Info::CENTINEL_ECI,
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE,
                    Info::PAYMENT_STATUS_GLOBAL => Info::PAYMENT_STATUS_GLOBAL,
                    Info::PENDING_REASON_GLOBAL => Info::PENDING_REASON_GLOBAL,
                    Info::IS_FRAUD_GLOBAL => Info::IS_FRAUD_GLOBAL
                ]
            ]
        ];
    }

}
