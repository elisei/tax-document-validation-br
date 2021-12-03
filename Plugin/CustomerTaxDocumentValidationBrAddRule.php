<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\TaxDocumentValidationBr\Plugin;

use Magento\Customer\Helper\Address;
use O2TI\TaxDocumentValidationBr\Helper\Config;

/**
 * Class CustomerTaxDocumentValidationBrAddRule - Add classes for validation atribute.
 */
class CustomerTaxDocumentValidationBrAddRule
{
    /**
     * @var Config
     */
    private $config;

    /**
     * TaxDocumentValidationBrAddRule constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Add Class to VatId.
     *
     * @param Address  $subject
     * @param callable $proceed
     * @param string   $args
     *
     * @return string
     */
    public function aroundGetAttributeValidationClass(Address $subject, callable $proceed, string $args): string
    {
        $result = $proceed($args);
        if ($args == 'vat_id') {
            if ($this->config->isEnabled()) {
                $add = 'required-entry ';
                if ($this->config->getConfigByVatId('enabled_cpf') && $this->config->getConfigByVatId('enabled_cnpj')) {
                    $add .= Config::VAT_CPF_OR_CNPJ;
                } elseif ($this->config->getConfigByVatId('enabled_cpf')) {
                    $add .= Config::VAT_ONLY_CPF;
                } elseif ($this->config->getConfigByVatId('enabled_cnpj')) {
                    $add .= Config::VAT_ONLY_CNPJ;
                }

                $result .= $add;
            }
        } elseif ($args == 'taxvat') {
            $add = ' ';
            if ($this->config->getConfigByTaxvat('enabled_cpf') && $this->config->getConfigByTaxvat('enabled_cnpj')) {
                $add .= Config::VAT_CPF_OR_CNPJ;
            } elseif ($this->config->getConfigByTaxvat('enabled_cpf')) {
                $add .= Config::VAT_ONLY_CPF;
            } elseif ($this->config->getConfigByTaxvat('enabled_cnpj')) {
                $add .= Config::VAT_ONLY_CNPJ;
            }

            $result .= $add;
        }

        return $result;
    }
}
