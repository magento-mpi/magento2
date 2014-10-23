Module Magento\Payment provides abstraction level for all payment methods - any logic that should be used by payment methods for integration into system checkout process. This logic contains configuration models, separate models for payment data verification and etc.
For example, Magento\Payment\Model\Method\AbstractMethod is an abstract model which should be extended by concrete payment methods.
