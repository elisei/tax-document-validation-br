<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\TaxDocumentValidationBr\Model;

use O2TI\TaxDocumentValidationBr\Helper\Config;

/**
 *  ValidationBr - Add Validation Brazilian for Tax Document.
 */
class ValidationBr
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Change Rule Validation.
     *
     * @param array $fields
     *
     * @return array
     */
    public function addRuleValidation(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key === 'vat_id') {
                if ($this->config->getConfigByVatId('enabled_cpf') && $this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['validation'] = [
                        'required-entry'        => 1,
                        Config::VAT_CPF_OR_CNPJ => 1,
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cpf')) {
                    $fields[$key]['validation'] = [
                        'required-entry'     => 1,
                        Config::VAT_ONLY_CPF => 1,
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['validation'] = [
                        'required-entry'      => 1,
                        Config::VAT_ONLY_CNPJ => 1,
                    ];
                }
            } elseif ($key === 'taxvat') {
                if ($this->config->getConfigByTaxvat('enabled_cpf')
                    && $this->config->getConfigByTaxvat('enabled_cnpj')
                ) {
                    $fields[$key]['validation'] = [
                        'required-entry'        => 1,
                        Config::VAT_CPF_OR_CNPJ => 1,
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cpf')) {
                    $fields[$key]['validation'] = [
                        'required-entry'     => 1,
                        Config::VAT_ONLY_CPF => 1,
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cnpj')) {
                    $fields[$key]['validation'] = [
                        'required-entry'      => 1,
                        Config::VAT_ONLY_CNPJ => 1,
                    ];
                }
            }
            continue;
        }

        return $fields;
    }

    /**
     * Add Tooltip.
     *
     * @param array $fields
     *
     * @return array
     */
    public function addTooltip(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key === 'vat_id') {
                if ($this->config->getConfigByVatId('enabled_cpf') && $this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF ou CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cpf')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                }
            } elseif ($key === 'taxvat') {
                if ($this->config->getConfigByTaxvat('enabled_cpf')
                    && $this->config->getConfigByTaxvat('enabled_cnpj')
                ) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF ou CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cpf')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cnpj')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                }
            }
            continue;
        }

        return $fields;
    }
}
