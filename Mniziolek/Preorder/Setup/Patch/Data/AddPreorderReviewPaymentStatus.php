<?php
/**
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */

namespace Mniziolek\Preorder\Setup\Patch\Data;

use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use \Mniziolek\Preorder\Model\Config\Source\Order\Status\PreorderReview as PreorderReviewConfigStatus;

/**
 * Payment preorder status patch
 */
class AddPreorderReviewPaymentStatus implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Sales\Model\Order\StatusFactory
     */
    protected $statusFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\StatusFactory
     */
    protected $statusResourceFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @param \Magento\Sales\Model\Order\StatusFactory $statusFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\StatusFactory $statusResourceFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Sales\Model\Order\StatusFactory               $statusFactory,
        \Magento\Sales\Model\ResourceModel\Order\StatusFactory $statusResourceFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface      $moduleDataSetup
    )
    {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->addNewOrderPreorderReviewStatus();
    }

    /**
     * Add new order status and assign it to state new
     *
     * @return void
     * @throws \Exception
     */
    protected function addNewOrderPreorderReviewStatus()
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var \Magento\Sales\Model\Order\Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => PreorderReviewConfigStatus::ORDER_STATUS_PREORDER_REVIEW_CODE,
            'label' => PreorderReviewConfigStatus::ORDER_STATUS_PREORDER_REVIEW_LABEL,
        ]);
        try {
            $statusResource->save($status);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $exception) {
            return;
        }
        $status->assignState(\Magento\Sales\Model\Order::STATE_NEW, false, true);
    }
}
