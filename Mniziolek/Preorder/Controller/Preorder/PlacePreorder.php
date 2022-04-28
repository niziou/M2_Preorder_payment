<?php
/**
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */
namespace Mniziolek\Preorder\Controller\Preorder;

use Magento\Framework\Controller\ResultFactory;

/**
 * PlacePreorder AJAX controller
 */
class PlacePreorder extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Mniziolek\Preorder\Model\Command\PreorderOrderResolver
     */
    protected $preorderOrderResolver;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mniziolek\Preorder\Model\Command\PreorderOrderResolver $preorderOrderResolver
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mniziolek\Preorder\Model\Command\PreorderOrderResolver $preorderOrderResolver
    )
    {
        parent::__construct($context);
        $this->preorderOrderResolver = $preorderOrderResolver;
    }

    /**
     * AJAX route for placing preorder from product page
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $orderData = $this->getRequest()->getParams();
        try
        {
            $orderId = $this->preorderOrderResolver->placeOrder($orderData);
            $response = [
                'status'=>'success',
                'message'=>'Order number #'.$orderId['success'].' was placed successfully'
            ];
        } catch (\Exception $e) {
            $response = [
                'status'=>'error',
                'message'=>'Order was not placed because: '.$e->getMessage()
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);

        return $resultJson;
    }
}
