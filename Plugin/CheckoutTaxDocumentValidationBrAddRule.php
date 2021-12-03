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
use O2TI\TaxDocumentValidationBr\Model\ValidationBr;

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
     * @param Config       $config
     * @param ValidationBr $validationBr
     */
    public function __construct(
        Config $config,
        ValidationBr $validationBr
    ) {
        $this->config = $config;
        $this->validationBr = $validationBr;
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
            $createAccountFields = $this->validationBr->addRuleValidation($createAccountFields);
            $createAccountFields = $this->validationBr->addTooltip($createAccountFields);
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
            $shippingFields = $this->validationBr->addRuleValidation($shippingFields);
            $shippingFields = $this->validationBr->addTooltip($shippingFields);
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
                    $billingFields = $this->validationBr->addRuleValidation($billingFields);
                    $billingFields = $this->validationBr->addTooltip($billingFields);
                }
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])
        ) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->validationBr->addRuleValidation($billingAddressOnPage);
            $billingAddressOnPage = $this->validationBr->addTooltip($billingAddressOnPage);
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'])
        ) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->validationBr->addRuleValidation($billingAddressOnPage);
            $billingAddressOnPage = $this->validationBr->addTooltip($billingAddressOnPage);
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
    public function aroundProcess(LayoutProcessor $layoutProcessor, callable $proceed, array $args): array
    {
        $jsLayout = $proceed($args);
        if ($this->config->isEnabled()) {
            $jsLayout = $this->changeCreateAccount($jsLayout);
            $jsLayout = $this->changeShippingFields($jsLayout);
            $jsLayout = $this->changeBillingFields($jsLayout);
            $layoutProcessor = $layoutProcessor;
        }

        return $jsLayout;
    }
}
