<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ShareCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShareCart\Plugin\Api;

use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderExtension;
use Magento\Quote\Model\QuoteFactory;
use Exception;

/**
 * Class CartManagementGetCustomer
 * @package Mageplaza\ShareCart\Plugin\Api
 */
class CartManagementGetCustomer
{
    /**
     * @var CartExtensionFactory
     */
    protected $cartExtensionFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * CartGet constructor.
     *
     * @param CartExtensionFactory $cartExtensionFactory
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(CartExtensionFactory $cartExtensionFactory, QuoteFactory $quoteFactory)
    {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->quoteFactory         = $quoteFactory;
    }

    /**
     * @param CartManagementInterface $cartManagement
     * @param CartInterface $cart
     *
     * @return CartInterface
     */
    public function afterGetCartForCustomer(CartManagementInterface $cartManagement, CartInterface $cart)
    {
        $cartExtension = $cart->getExtensionAttributes();
        if ($cartExtension && $cartExtension->getMpShareCartToken()) {
            return $cart;
        }

        try {
            $mpShareCartToken = $this->quoteFactory->create()->load($cart->getId())->getMpShareCartToken();
        } catch (Exception $e) {
            return $cart;
        }

        /** @var OrderExtension $orderExtension */
        $cartExtension = $cartExtension ?: $this->cartExtensionFactory->create();

        $cartExtension->setMpShareCartToken($mpShareCartToken);
        $cart->setExtensionAttributes($cartExtension);

        return $cart;
    }
}