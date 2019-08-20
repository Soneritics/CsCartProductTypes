{if $product['SoneriticsProductTypes']}
    <div id="pt-holder">
        <p>Andere kleuren/maten:</p>

        {foreach from=$product['SoneriticsProductTypes'] item="producttype"}
            <a href="{$producttype.url}" class="pt-btn{if $producttype.product_id == $product.product_id} pt-active{/if}">
                {$producttype.SoneriticsProductTypeDisplayValue}
            </a>
        {/foreach}
    </div>
{/if}