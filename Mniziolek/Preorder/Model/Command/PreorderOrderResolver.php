<?php
/**
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */
namespace Mniziolek\Preorder\Model\Command;

/**
 * Command programatically place order from product page.
 */
class PreorderOrderResolver
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * @var \Magento\Quote\Model\Quote\Address\Rate
     */
    protected $shippingRate;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Magento\Quote\Model\Quote\Address\Rate $shippingRate
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface         $orderRepository,
        \Magento\Quote\Model\QuoteRepository                $quoteRepository,
        \Magento\Store\Model\StoreManagerInterface          $storeManager,
        \Magento\Customer\Model\CustomerFactory             $customerFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface   $customerRepository,
        \Magento\Quote\Model\QuoteFactory                   $quote,
        \Magento\Quote\Model\QuoteManagement                $quoteManagement,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Quote\Model\Quote\Address\Rate $shippingRate
    )
    {
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->orderSender = $orderSender;
        $this->shippingRate = $shippingRate;
    }

    /**
     * Place Preorder Order
     *
     * @param array $orderData
     * @return array
     */
    public function placeOrder($orderData)
    {
        $orderInfo = [
            'email' => $orderData['email'], //customer email id
            'currency_id' => 'NOK',
            'address' => [
                'firstname' => $orderData['firstname'],
                'lastname' => $orderData['surname'],
                'prefix' => '',
                'suffix' => '',
                'street' => $orderData['street0'].$orderData['street1'],
                'city' => $orderData['city'],
                'country_id' => 'NO',
                'postcode' => $orderData['zip'],
                'telephone' => $orderData['mobile'],
                'save_in_address_book' => 1
            ],
            'items' =>
                [
                    [
                        'product_id' => $orderData['product_id'],
                        'qty' => 1
                    ],
                ]
        ];
        $store = $this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        $quote = $this->quote->create();

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId)->loadByEmail($orderInfo['email']);

        if (!$customer->getEntityId()) {
            $quote->setCustomerFirstname($orderInfo['address']['firstname']);
            $quote->setCustomerLastname($orderInfo['address']['lastname']);
            $quote->setCustomerEmail($orderInfo['email']);
            $quote->setCustomerIsGuest(true);
        } else {
            $quote->assignCustomer($customer->getDataModel());
        }

        $quote->setStore($store);
        $quote->setCurrency();

        //Add Items in Quote Object
        foreach ($orderInfo['items'] as $item) {
            $product = $this->productRepository->getById($item['product_id']);
            if (!empty($item['super_attribute'])) {
                /**
                 * Configurable Product
                 */
                $buyRequest = new \Magento\Framework\DataObject($item);
                $quote->addProduct($product, $buyRequest);
            } else {
                /**
                 * Simple Product
                 */
                $quote->addProduct($product, intval($item['qty']));
            }
        }

        //Billing & Shipping Address to Quote
        $quote->getBillingAddress()->addData($orderInfo['address']);
        $quote->getShippingAddress()->addData($orderInfo['address']);

        // Set Shipping Method
        $this->shippingRate
            ->setCarrier('pickupinstore')
            ->setMethod('pickupinstore')
            ->setCode('pickupinstore_pickupinstore')
            ->getPrice(1);

        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->addShippingRate($this->shippingRate)
            ->setShippingMethod('pickupinstore_pickupinstore');
        $quote->setPaymentMethod('preorder');
        $quote->setInventoryProcessed(false);
        $quote->setIsMultiShipping(0);
        $this->quoteRepository->save($quote);
        $quote->getPayment()->importData(['method' => 'preorder']);
        // Collect Quote Totals
        $quote->collectTotals();
        // Create Order From Quote Object
        $order = $this->quoteManagement->submit($quote);
        // Send Order Email to Customer Email ID
        $this->orderSender->send($order);

        if (!is_null($order)) {
            $result['success'] = $order->getIncrementId();
        } else {
            $result = ['error' => true, 'msg' => __('Error occurs for Order placed')];
        }

        $order->setState(\Magento\Sales\Model\Order::STATE_NEW);
        $order->setStatus(
            \Mniziolek\Preorder\Model\Config\Source\Order\Status\PreorderReview::ORDER_STATUS_PREORDER_REVIEW_CODE
        );

        $this->orderRepository->save($order);

        return $result;
    }

}
