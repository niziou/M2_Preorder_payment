<?php
/**
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */

namespace Mniziolek\Preorder\Model\ConfigProvider;

/**
 * Payment Method Config Provider
 */
class PreorderPaymentConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Mniziolek\Preorder\Helper\PreorderConfig
     */
    protected $preorderConfig;

    /**
     * @param \Mniziolek\Preorder\Helper\PreorderConfig $preorderConfig
     */
    public function __construct(\Mniziolek\Preorder\Helper\PreorderConfig $preorderConfig)
    {
        $this->preorderConfig = $preorderConfig;
    }

    /**
     * Checkout payment flag option
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                'preorder' => [
                    'isActive' => $this->isPaymentMethodActive()
                ]
            ]
        ];
    }

    /**
     * Check payment method available
     *
     * @return bool
     */
    protected function isPaymentMethodActive()
    {
        return
            $this->preorderConfig->isPreoderModuleEnabled() &&
            $this->preorderConfig->isPreorderPaymentEnabled() &&
            $this->preorderConfig->isPreorderItemInCart();
    }
}
