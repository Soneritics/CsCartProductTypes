<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_soneritics_producttypes_get_product_data_post(&$product, &$auth)
{
    if (!empty($product['product_id'])) {
        $productId = (int)$product['product_id'];

        $linkedProductIds = array_merge(
            db_get_fields('SELECT product2_id FROM ?:soneritics_producttypes WHERE product1_id = ?i', $productId),
            db_get_fields('SELECT product1_id FROM ?:soneritics_producttypes WHERE product2_id = ?i', $productId),
            [$productId]
        );

        if (count($linkedProductIds) > 1) {
            $products = fn_get_products([
                'pid' => $linkedProductIds,
                'force_get_by_ids' => true,
                'get_frontend_urls' => true
            ]);
        }

        if (!empty($products[0])) {
            fn_gather_additional_products_data(
                $products[0], [
                    'get_icon' => false,
                    'get_detailed' => false,
                    'get_additional' => false,
                    'get_options' => false,
                    'get_discounts' => false,
                    'get_features' => true,
                    'features_display_on' => 'A'
                ]
            );

            foreach ($products[0] as &$_product) {
                $_product['SoneriticsProductTypeDisplayValue'] = $_product['product'];

                if (!empty($_product['product_features'])) {
                    foreach ($_product['product_features'] as $productFeature) {
                        if (strtolower($productFeature['description']) == 'producttype' && !empty($productFeature['value'])) {
                            $_product['SoneriticsProductTypeDisplayValue'] = $productFeature['value'];
                        }
                    }
                }
            }

            $product['SoneriticsProductTypes'] = $products[0];
        }
    }
}
