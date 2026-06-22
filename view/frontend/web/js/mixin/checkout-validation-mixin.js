/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define(["jquery", "Magento_Ui/js/lib/validation/utils"], function ($, utils) {
	"use strict";

	return function (validator) {
		var normalizeDigits = function (value) {
			return (value || "").replace(/[^\d]+/g, "");
		};

		var normalizeAlphaNumeric = function (value) {
			return (value || "").replace(/[^0-9a-z]+/gi, "").toUpperCase();
		};

		var hasLetters = function (value) {
			return /[A-Z]/.test(normalizeAlphaNumeric(value));
		};

		var cnpjFirstWeights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
		var cnpjSecondWeights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

		var getAlphaNumericValue = function (character) {
			return character.charCodeAt(0) - 48;
		};

		var calculateCheckDigit = function (base, weights) {
			var sum = 0;

			base.split("").forEach(function (character, index) {
				sum += getAlphaNumericValue(character) * weights[index];
			});

			var remainder = sum % 11;
			return remainder < 2 ? 0 : 11 - remainder;
		};

		/**
		 * Invalidate Common CNPJ
		 */
		var invalidosComunsCNPJ = function (value) {
			if (
				value === "00000000000000" ||
				value === "11111111111111" ||
				value === "22222222222222" ||
				value === "33333333333333" ||
				value === "44444444444444" ||
				value === "55555555555555" ||
				value === "66666666666666" ||
				value === "77777777777777" ||
				value === "88888888888888" ||
				value === "99999999999999"
			) {
				return true;
			}
			return false;
		};

		/**
		 * Invalidate Common CNPJ
		 */
		var invalidosComunsCPF = function (value) {
			if (
				value === "00000000000" ||
				value === "11111111111" ||
				value === "22222222222" ||
				value === "33333333333" ||
				value === "44444444444" ||
				value === "55555555555" ||
				value === "66666666666" ||
				value === "77777777777" ||
				value === "88888888888" ||
				value === "99999999999"
			) {
				return true;
			}
			return false;
		};

		/**
		 * Validate CPF
		 */
		var validateCPF = function (value) {
			let cpf = normalizeDigits(value);

			if (cpf.length !== 11) {
				return false;
			}

			if (invalidosComunsCPF(cpf)) {
				return false;
			}

			let add = 0;
			let i;
			let j;
			let rev;
			for (i = 0; i < 9; i++) {
				add += parseInt(cpf.charAt(i), 10) * (10 - i);
			}

			rev = 11 - (add % 11);
			if (rev === 10 || rev === 11) {
				rev = 0;
			}
			if (rev !== parseInt(cpf.charAt(9), 10)) {
				return false;
			}

			add = 0;
			for (j = 0; j < 10; j++) {
				add += parseInt(cpf.charAt(j), 10) * (11 - j);
			}

			rev = 11 - (add % 11);

			if (rev === 10 || rev === 11) {
				rev = 0;
			}

			if (rev !== parseInt(cpf.charAt(10), 10)) {
				return false;
			}

			return true;
		};

		/**
		 * Validate CNPJ
		 */
		var validateCNPJ = function (value) {
			let cnpj = normalizeDigits(value);

			if (cnpj.length !== 14) {
				return false;
			}

			if (invalidosComunsCNPJ(cnpj)) {
				return false;
			}

			let tamanho = cnpj.length - 2;
			let numeros = cnpj.substring(0, tamanho);
			let digitos = cnpj.substring(tamanho);
			let soma = 0;
			let pos = tamanho - 7;
			let i;
			let j;
			let resultado;
			for (i = tamanho; i >= 1; i--) {
				soma += numeros.charAt(tamanho - i) * pos--;
				if (pos < 2) {
					pos = 9;
				}
			}
			resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);

			if (resultado !== parseInt(digitos.charAt(0), 10)) {
				return false;
			}

			tamanho = tamanho + 1;
			numeros = cnpj.substring(0, tamanho);
			soma = 0;
			pos = tamanho - 7;
			for (j = tamanho; j >= 1; j--) {
				soma += numeros.charAt(tamanho - j) * pos--;
				if (pos < 2) {
					pos = 9;
				}
			}
			resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);

			if (resultado !== parseInt(digitos.charAt(1), 10)) {
				return false;
			}

			return true;
		};

		/**
		 * Validate alphanumeric CNPJ format.
		 */
		var validateAlphaNumericCNPJ = function (value) {
			let cnpj = normalizeAlphaNumeric(value);

			if (cnpj.length !== 14) {
				return false;
			}

			if (!/[A-Z]/.test(cnpj)) {
				return validateCNPJ(cnpj);
			}

			if (!/^[A-Z0-9]{12}\d{2}$/.test(cnpj)) {
				return false;
			}

			var base = cnpj.substring(0, 12);
			var firstDigit = calculateCheckDigit(base, cnpjFirstWeights);
			var secondDigit = calculateCheckDigit(base + firstDigit, cnpjSecondWeights);

			return cnpj === base + firstDigit.toString() + secondDigit.toString();
		};
		
		/**
		 * Add Validation CPF/CNPJ
		 */
		validator.addRule(
			"vatid-br-rule-cpf-or-cnpj",
				function (value) {
					var normalizedAlphaNumeric = normalizeAlphaNumeric(value);
					var normalizedDigits = normalizeDigits(value);

					if (hasLetters(value) && normalizedAlphaNumeric.length === 14) {
						return validateAlphaNumericCNPJ(value);
					}
					if (normalizedDigits.length === 14) {
						return validateCNPJ(value);
					}
					if (normalizedDigits.length === 11) {
						return validateCPF(value);
					}

					return false;
				},
				$.mage.__('Please provide a valid tax document (CPF/CNPJ)')
		);

		/**
		 * Add Validation CPF
		 */
		validator.addRule(
			 "vatid-br-rule-only-cpf",
				function (value) {
					if (normalizeDigits(value).length === 11) {
						return validateCPF(value);
					}

					return false;
				},
				$.mage.__("Please provide a valid tax document (CPF)")
		);

		/**
		 * Add Validation CNPJ
		 */
		validator.addRule(
			 "vatid-br-rule-only-cnpj",
				function (value) {
					if (hasLetters(value) && normalizeAlphaNumeric(value).length === 14) {
						return validateAlphaNumericCNPJ(value);
					}
					if (normalizeDigits(value).length === 14) {
						return validateCNPJ(value);
					}

					return false;
				},
				$.mage.__("Please provide a valid tax document (CNPJ)")
		);

		return validator;
	};
});
