<?php

/**
 * Attribute Prices
 *
 * @author  Jay Trees <attribute-prices@grandels.email>
 * @link    https://github.com/Grandeljay/modified-attribute-prices
 */

namespace Grandeljay\AttributePrices;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - Prezzi degli attributi');
$translations->add('TEXT_TITLE', 'Prezzi degli attributi');

$translations->define();
