<?php

/**
 * Attribute Prices
 *
 * @author  Jay Trees <attribute-prices@grandels.email>
 * @link    https://github.com/Grandeljay/modified-attribute-prices
 */

namespace Grandeljay\AttributePrices;

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

if (!empty($products_options['attributes_model'])) {
    $products_data_query = \xtc_db_query(
        \sprintf(
            'SELECT *
               FROM `%s`
              WHERE `products_model` = "%s"',
            \TABLE_PRODUCTS,
            $products_options['attributes_model']
        )
    );
    $products_data       = \xtc_db_fetch_array($products_data_query);

    if (!isset($products_data['products_id'])) {
        return;
    }

    $currency_id         = $currencies_array[0]['id'];
    $customers_status_id = $customer_status['customers_status'] ?? \DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
    $products_id         = $products_data['products_id'];

    $xtc_price      = new \xtcPrice($currency_id, $customers_status_id);
    $products_price = $xtc_price->xtcGetPrice($products_id, false);

    $products_options_data[$row]['DATA'][$col]['PRICE']       = $xtc_price->xtcFormat($products_price, true);
    $products_options_data[$row]['DATA'][$col]['FULL_PRICE']  = $xtc_price->xtcFormat($products_price, true);
    $products_options_data[$row]['DATA'][$col]['PLAIN_PRICE'] = $xtc_price->xtcFormat($products_price, false);
}
