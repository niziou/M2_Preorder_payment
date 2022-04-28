<?php
/**
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */
namespace Mniziolek\Preorder\Model\Payment\Method;

/**
 * Payment method class
 */
class Preorder extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PREORDER_PAYMENT_CODE = 'preorder';

    /**
     * @var string
     */
    protected $_code = self::PREORDER_PAYMENT_CODE;

    /**
     * @var bool
     */
    protected $_isOffline = true;
}
