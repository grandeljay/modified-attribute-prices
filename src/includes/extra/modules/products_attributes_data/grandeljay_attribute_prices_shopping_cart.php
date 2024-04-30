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
    /**
     * Get product id of attribute
     */
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

    /**
     * Get personal offer
     */
    $customers_status_id = $customer_status['customers_status'] ?? \DEFAULT_CUSTOMERS_STATUS_ID_GUEST;

    $personal_offer_sql   = sprintf(
        'SELECT *
           FROM `%s`
          WHERE `products_id` = %d
            AND `quantity`    = %d',
        TABLE_PERSONAL_OFFERS_BY . $customers_status_id,
        $products_data['products_id'],
        1
    );
    $personal_offer_query = \xtc_db_query($personal_offer_sql);
    $personal_offer_data  = \xtc_db_fetch_array($personal_offer_query);
    $personal_offer       = $personal_offer_data['personal_offer'] ?? null;

    /**
     * Format price
     */
    $currency_id         = $currencies_array[0]['id'];
    $customers_status_id = $customer_status['customers_status'] ?? \DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
    $products_id         = $products_data['products_id'];

    $xtc_price      = new \xtcPrice($currency_id, $customers_status_id);
    $products_price = $personal_offer;

    $products_options_data[$row]['DATA'][$col]['PRICE']       = $xtc_price->xtcFormat($products_price, true);
    $products_options_data[$row]['DATA'][$col]['FULL_PRICE']  = $xtc_price->xtcFormat($products_price, true);
    $products_options_data[$row]['DATA'][$col]['PLAIN_PRICE'] = $xtc_price->xtcFormat($products_price, false);
}
