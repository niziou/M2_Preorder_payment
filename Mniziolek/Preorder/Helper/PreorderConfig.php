<?php
/**
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */

namespace Mniziolek\Preorder\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Preorder helper
 */
class PreorderConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_MODULE_ENABLED = 'mniziolek_preorder/general/status';

    const XML_PATH_PAYMENT_METHOD_ENABLED = 'payment/preorder/active';

    /**
     * Magento Checkout Session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get scope config value of Preorder feature disable/enable status
     *
     * @return bool
     */
    public function isPreoderModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MODULE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Payment method flag check
     *
     * @return bool
     */
    public function isPreorderPaymentEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_METHOD_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check quote item for preorder attribute true.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isPreorderItemInCart()
    {
        $result = false;
        foreach($this->checkoutSession->getQuote()->getAllVisibleItems() as $item) {
            if($item->getProduct()->getPreorder() == '1') {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Check product attribute preorder
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isProductPreorderAvailable($product)
    {
        $result = false;
        if ($product->getPreorder() == '1') {
            $result = true;
        }

        return $result;
    }
}
