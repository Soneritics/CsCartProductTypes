<?php
/*
 * The MIT License
 *
 * Copyright 2019 Jordi Jolink.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'update') {
    // Define product ID
    $productId = empty($_REQUEST['product_id']) ? 0 : (int)$_REQUEST['product_id'];

    // Process form submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // First clear the database
        db_query(
            'DELETE FROM ?:soneritics_producttypes WHERE product1_id = ?i OR product2_id = ?i',
            $productId,
            $productId
        );

        // Process new product types
        if (!empty($_REQUEST['product_types'])) {
            $productTypes = explode(',', $_REQUEST['product_types']);
            foreach ($productTypes as $productTypeId) {
                $productId2 = (int)$productTypeId;
                if (!empty($productId2) && $productId !== $productId2) {
                    db_query(
                        'INSERT INTO ?:soneritics_producttypes(`product1_id`, `product2_id`) VALUES(?i, ?i)',
                        $productId,
                        $productId2
                    );
                }
            }
        }
    }

    // Add product tab
    Registry::set(
        'navigation.tabs.product_types',
        [
            'title' => __('product_types'),
            'js' => true
        ]
    );

    // Set current product types
    $productTypes = array_merge(
        db_get_fields('SELECT product2_id FROM ?:soneritics_producttypes WHERE product1_id = ?i', $productId),
        db_get_fields('SELECT product1_id FROM ?:soneritics_producttypes WHERE product2_id = ?i', $productId)
    );
    Tygh::$app['view']->assign('productTypes', $productTypes);
}
