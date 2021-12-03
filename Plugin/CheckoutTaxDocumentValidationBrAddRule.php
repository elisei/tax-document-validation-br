<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\TaxDocumentValidationBr\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use O2TI\TaxDocumentValidationBr\Helper\Config;
use O2TI\TaxDocumentValidationBr\Model\BrazilianDocument;

/**
 * Class CheckoutTaxDocumentValidationBrAddRule - Change componentes for validation atribute.
 */
class CheckoutTaxDocumentValidationBrAddRule
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var BrazilianDocument
     */
    private $brazilianDocument;

    /**
     * @param Config            $config
     * @param BrazilianDocument $brazilianDocument
     */
    public function __construct(
        Config $config,
        BrazilianDocument $brazilianDocument
    ) {
        $this->config = $config;
        $this->brazilianDocument = $brazilianDocument;
    }

    /**
     * Change Components in Create Account.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function changeCreateAccount(array $jsLayout): ?array
    {
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'])
        ) {
            // phpcs:ignore
            $createAccountFields = &$jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'];
            $createAccountFields = $this->brazilianDocument->addRuleValidation($createAccountFields);
            $createAccountFields = $this->brazilianDocument->addTooltip($createAccountFields);
        }

        return $jsLayout;
    }

    /**
     * Change Components in Shipping.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function changeShippingFields(array $jsLayout): ?array
    {
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])
        ) {
            // phpcs:ignore
            $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $shippingFields = $this->brazilianDocument->addRuleValidation($shippingFields);
            $shippingFields = $this->brazilianDocument->addTooltip($shippingFields);
        }

        return $jsLayout;
    }

    /**
     * Change Components in Billing.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function changeBillingFields(array $jsLayout): array
    {
        // phpcs:ignore
        foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as &$payment) {
            if (isset($payment['children']['form-fields'])) {
                if (isset($payment['children']['form-fields']['children'])) {
                    $billingFields = &$payment['children']['form-fields']['children'];
                    $billingFields = $this->brazilianDocument->addRuleValidation($billingFields);
                    $billingFields = $this->brazilianDocument->addTooltip($billingFields);
                }
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])
        ) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->brazilianDocument->addRuleValidation($billingAddressOnPage);
            $billingAddressOnPage = $this->brazilianDocument->addTooltip($billingAddressOnPage);
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'])
        ) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->brazilianDocument->addRuleValidation($billingAddressOnPage);
            $billingAddressOnPage = $this->brazilianDocument->addTooltip($billingAddressOnPage);
        }

        return $jsLayout;
    }

    /**
     * Select Components for Change.
     *
     * @param LayoutProcessor $layoutProcessor
     * @param callable        $proceed
     * @param array           $args
     *
     * @return array
     */
    public function aroundProcess(
        /** @scrutinizer ignore-unused */ LayoutProcessor $layoutProcessor,
        callable $proceed,
        array $args
    ): array {
        $jsLayout = $proceed($args);
        if ($this->config->isEnabled()) {
            $jsLayout = $this->changeCreateAccount($jsLayout);
            $jsLayout = $this->changeShippingFields($jsLayout);
            $jsLayout = $this->changeBillingFields($jsLayout);
        }

        return $jsLayout;
    }
}
