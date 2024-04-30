<?php

/**
 * Attribute Prices
 *
 * @author  Jay Trees <attribute-prices@grandels.email>
 * @link    https://github.com/Grandeljay/modified-attribute-prices
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */

use Grandeljay\AttributePrices\Constants;
use RobinTheHood\ModifiedStdModule\Classes\StdModule;

class grandeljay_attribute_prices_shopping_cart extends StdModule
{
    public const VERSION = '0.2.3';

    public function __construct()
    {
        parent::__construct(Constants::MODULE_NAME);

        $this->checkForUpdate(true);
    }

    protected function updateSteps(): int
    {
        if (version_compare($this->getVersion(), self::VERSION, '<')) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    /**
     * Gets the price for a product option.
     *
     * @param float $price
     * @param int   $options_id
     * @param int   $options_value_id
     * @param mixed $products_id_options_values
     * @param int   $quantity
     *
     * @return float
     *
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function calculate_option_price(float $price, int $options_id, int $options_value_id, mixed $products_id_options_values, int $quantity): float
    {
        preg_match('/\d+/', $products_id_options_values, $products_id_matches);

        $products_id = $products_id_matches[0];

        /**
         * Get product id of attribute
         */
        $products_attribute_id_sql   = sprintf(
            '  SELECT `%2$s`.`products_id`
                 FROM `%1$s`
            LEFT JOIN `%2$s` ON `%1$s`.`attributes_model` = `%2$s`.`products_model`
                WHERE `%1$s`.`products_id`       = %3$d
                  AND `%1$s`.`options_values_id` = %4$d',
            TABLE_PRODUCTS_ATTRIBUTES,
            TABLE_PRODUCTS,
            $products_id,
            $options_value_id
        );
        $products_attribute_id_query = xtc_db_query($products_attribute_id_sql);
        $products_attribute_id_data  = xtc_db_fetch_array($products_attribute_id_query);
        $products_attribute_id       = $products_attribute_id_data['products_id'] ?? null;

        if (null === $products_attribute_id) {
            return $price;
        }

        /**
         * Get personal offer
         */
        $customers_status_id = $_SESSION['customers_status']['customers_status_id'] ?? DEFAULT_CUSTOMERS_STATUS_ID_GUEST;

        $personal_offer_sql   = sprintf(
            'SELECT *
               FROM `%s`
              WHERE `products_id` = %d
                AND `quantity`    = %d',
            TABLE_PERSONAL_OFFERS_BY . $customers_status_id,
            $products_attribute_id,
            $quantity
        );
        $personal_offer_query = xtc_db_query($personal_offer_sql);
        $personal_offer_data  = xtc_db_fetch_array($personal_offer_query);
        $personal_offer       = $personal_offer_data['personal_offer'] ?? null;

        if (null === $personal_offer) {
            return $price;
        }

        return $personal_offer;
    }
    /**
     * @phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
}
