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
            'Magento\Paypal\Model\Info',
            [
                
            ]
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
                    'paypal_payer_id' => 'paypal_payer_id',
                    'paypal_payer_email' => 'paypal_payer_email',
                    'paypal_payer_status' => 'paypal_payer_status',
                    'paypal_address_id' => 'paypal_address_id',
                    'paypal_address_status' => 'paypal_address_status',
                    'paypal_protection_eligibility' => 'paypal_protection_eligibility',
                    'paypal_fraud_filters' => 'paypal_fraud_filters',
                    'paypal_correlation_id' => 'paypal_correlation_id',
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::PAYPAL_AVS_CODE => 'A',
                    Info::PAYPAL_CVV2_MATCH => 'M',
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE_CNPJ,
                    Info::CENTINEL_VPAS => '2',
                    Info::CENTINEL_ECI => '01'
                ],
                [
                    'paypal_payer_id' => [
                        'label' => 'Payer ID',
                        'value' => 'paypal_payer_id',
                    ],
                    'paypal_payer_email' => [
                        'label' => 'Payer Email',
                        'value' => 'paypal_payer_email',
                     ],
                    'paypal_payer_status' => [
                        'label' => 'Payer Status',
                        'value' => 'paypal_payer_status',
                    ],
                    'paypal_address_id' => [
                        'label' => 'Payer Address ID',
                        'value' => 'paypal_address_id',
                    ],
                    'paypal_address_status' => [
                        'label' => 'Payer Address Status',
                        'value' => 'paypal_address_status',
                    ],
                    'paypal_protection_eligibility' => [
                        'label' => 'Merchant Protection Eligibility',
                        'value' => 'paypal_protection_eligibility',
                    ],
                    'paypal_fraud_filters' => [
                        'label' => 'Triggered Fraud Filters',
                        'value' => 'paypal_fraud_filters',
                    ],
                    'paypal_correlation_id' => [
                        'label' => 'Last Correlation ID',
                        'value' => 'paypal_correlation_id',
                    ],
                    'paypal_avs_code' => [
                        'label' => 'Address Verification System Response',
                        'value' => '#A: Matched Address only (no ZIP)',
                    ],
                    'paypal_cvv2_match' => [
                        'label' => 'CVV2 Check Result by PayPal',
                        'value' => '#M: Matched (CVV2CSC)',
                    ],
                    'centinel_vpas_result' => [
                        'label' => 'PayPal/Centinel Visa Payer Authentication Service Result',
                        'value' => '#2: Authenticated, Good Result',
                    ],
                    'centinel_eci_result' => [
                        'label' => 'PayPal/Centinel Electronic Commerce Indicator',
                        'value' => '#01: Merchant Liability',
                    ],
                    'buyer_tax_id' => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => 'buyer_tax_id',
                    ],
                    'buyer_tax_id_type' => [
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
                    'paypal_payer_id' => 'paypal_payer_id',
                    'paypal_payer_email' => 'paypal_payer_email',
                    'paypal_payer_status' => 'paypal_payer_status',
                    'paypal_address_id' => 'paypal_address_id',
                    'paypal_address_status' => 'paypal_address_status',
                    'paypal_protection_eligibility' => 'paypal_protection_eligibility',
                    'paypal_fraud_filters' => 'paypal_fraud_filters',
                    'paypal_correlation_id' => 'paypal_correlation_id',
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::PAYPAL_AVS_CODE => Info::PAYPAL_AVS_CODE,
                    Info::PAYPAL_CVV2_MATCH => Info::PAYPAL_CVV2_MATCH,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE,
                    Info::CENTINEL_VPAS => Info::CENTINEL_VPAS,
                    Info::CENTINEL_ECI => Info::CENTINEL_ECI
                ],
                [
                    'paypal_payer_id' => [
                        'label' => 'Payer ID',
                        'value' => 'paypal_payer_id',
                    ],
                    'paypal_payer_email' => [
                        'label' => 'Payer Email',
                        'value' => 'paypal_payer_email',
                    ],
                    'paypal_payer_status' => [
                        'label' => 'Payer Status',
                        'value' => 'paypal_payer_status',
                    ],
                    'paypal_address_id' => [
                        'label' => 'Payer Address ID',
                        'value' => 'paypal_address_id',
                    ],
                    'paypal_address_status' => [
                        'label' => 'Payer Address Status',
                        'value' => 'paypal_address_status',
                    ],
                    'paypal_protection_eligibility' => [
                        'label' => 'Merchant Protection Eligibility',
                        'value' => 'paypal_protection_eligibility',
                    ],
                    'paypal_fraud_filters' => [
                        'label' => 'Triggered Fraud Filters',
                        'value' => 'paypal_fraud_filters',
                    ],
                    'paypal_correlation_id' => [
                        'label' => 'Last Correlation ID',
                        'value' => 'paypal_correlation_id',
                    ],
                    'paypal_avs_code' => [
                        'label' => 'Address Verification System Response',
                        'value' => '#paypal_avs_code',
                    ],
                    'paypal_cvv2_match' => [
                        'label' => 'CVV2 Check Result by PayPal',
                        'value' => '#paypal_cvv2_match',
                    ],
                    'centinel_vpas_result' => [
                        'label' => 'PayPal/Centinel Visa Payer Authentication Service Result',
                        'value' => '#centinel_vpas_result',
                    ],
                    'centinel_eci_result' => [
                        'label' => 'PayPal/Centinel Electronic Commerce Indicator',
                        'value' => '#centinel_eci_result',
                    ],
                    'buyer_tax_id' => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => 'buyer_tax_id',
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
                    'paypal_payer_email' => 'paypal_payer_email',
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE_CNPJ
                ],
                [
                    'paypal_payer_email' => [
                        'label' => 'Payer Email',
                        'value' => 'paypal_payer_email',
                    ],
                    'buyer_tax_id' => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => 'buyer_tax_id',
                    ],
                    'buyer_tax_id_type' => [
                        'label' => 'Buyer\'s Tax ID Type',
                        'value' => 'CNPJ',
                    ]
                ]
            ],
            [
                [
                    'paypal_payer_email' => 'paypal_payer_email',
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE
                ],
                [
                    'paypal_payer_email' => [
                        'label' => 'Payer Email',
                        'value' => 'paypal_payer_email',
                    ],
                    'buyer_tax_id' => [
                        'label' => 'Buyer\'s Tax ID',
                        'value' => 'buyer_tax_id',
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
                    Info::PAYER_ID => 'paypal_payer_id',
                    Info::PAYER_EMAIL => 'paypal_payer_email',
                    Info::PAYER_STATUS => 'paypal_payer_status',
                    Info::ADDRESS_ID => 'paypal_address_id',
                    Info::ADDRESS_STATUS => 'paypal_address_status',
                    Info::PROTECTION_EL => 'paypal_protection_eligibility',
                    Info::FRAUD_FILTERS => 'paypal_fraud_filters',
                    Info::CORRELATION_ID => 'paypal_correlation_id',
                    Info::AVS_CODE => 'paypal_avs_code',
                    Info::CVV2_MATCH => 'paypal_cvv2_match',
                    Info::CENTINEL_VPAS => Info::CENTINEL_VPAS,
                    Info::CENTINEL_ECI => Info::CENTINEL_ECI,
                    Info::BUYER_TAX_ID => Info::BUYER_TAX_ID,
                    Info::BUYER_TAX_ID_TYPE => Info::BUYER_TAX_ID_TYPE,
                    Info::PAYMENT_STATUS => Info::PAYMENT_STATUS_GLOBAL,
                    Info::PENDING_REASON => Info::PENDING_REASON_GLOBAL,
                    Info::IS_FRAUD => Info::IS_FRAUD_GLOBAL
                ],
                [
                    'paypal_payer_id' => 'paypal_payer_id',
                    'paypal_payer_email' => 'paypal_payer_email',
                    'paypal_payer_status' => 'paypal_payer_status',
                    'paypal_address_id' => 'paypal_address_id',
                    'paypal_address_status' => 'paypal_address_status',
                    'paypal_protection_eligibility' => 'paypal_protection_eligibility',
                    'paypal_fraud_filters' => 'paypal_fraud_filters',
                    'paypal_correlation_id' => 'paypal_correlation_id',
                    'paypal_avs_code' => 'paypal_avs_code',
                    'paypal_cvv2_match' => 'paypal_cvv2_match',
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
