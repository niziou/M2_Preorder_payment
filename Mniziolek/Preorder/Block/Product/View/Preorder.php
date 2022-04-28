<?php
/**
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */
namespace Mniziolek\Preorder\Block\Product\View;

/**
 * Product page block for preorder button
 */
class Preorder extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Mniziolek\Preorder\Helper\PreorderConfig
     */
    protected $preorderConfigHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Mniziolek\Preorder\Helper\PreorderConfig $preorderConfigHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Mniziolek\Preorder\Helper\PreorderConfig $preorderConfigHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->preorderConfigHelper = $preorderConfigHelper;
    }

    /**
     * Validation if button can be shown
     *
     * @return bool
     */
    public function isPreorderActive()
    {
        return $this->preorderConfigHelper->isPreoderModuleEnabled() &&
            $this->preorderConfigHelper->isPreorderPaymentEnabled() &&
            $this->preorderConfigHelper->isProductPreorderAvailable($this->getProduct());
    }
}
