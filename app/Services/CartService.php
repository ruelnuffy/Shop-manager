<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'cart';

    public function getItems(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function add(int $variantId, int $quantity = 1): array
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        if ($variant->stock_quantity < 1) {
            throw new \Exception('Sorry, this item is sold out.');
        }

        $items = $this->getItems();

        if (isset($items[$variantId])) {
            $newQuantity = $items[$variantId]['quantity'] + $quantity;
            if ($newQuantity > $variant->stock_quantity) {
                $newQuantity = $variant->stock_quantity;
            }
            $items[$variantId]['quantity'] = $newQuantity;
        } else {
            $items[$variantId] = [
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_name' => $variant->product->name,
                'product_slug' => $variant->product->slug,
                'image' => $variant->product->featured_image_url ?? ($variant->product->featured_image ? asset(ltrim($variant->product->featured_image, '/')) : null),
                'size' => $variant->size,
                'color' => $variant->color,
                'sku' => $variant->sku,
                'price' => (float) $variant->effective_price,
                'quantity' => min($quantity, $variant->stock_quantity),
                'stock_limit' => $variant->stock_quantity,
            ];
        }

        Session::put($this->sessionKey, $items);

        return $items[$variantId];
    }

    public function update(int $variantId, int $quantity): void
    {
        $items = $this->getItems();

        if (! isset($items[$variantId])) {
            return;
        }

        $variant = ProductVariant::findOrFail($variantId);

        if ($quantity <= 0) {
            $this->remove($variantId);

            return;
        }

        $items[$variantId]['quantity'] = min($quantity, $variant->stock_quantity);
        Session::put($this->sessionKey, $items);
    }

    public function remove(int $variantId): void
    {
        $items = $this->getItems();
        unset($items[$variantId]);
        Session::put($this->sessionKey, $items);
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    public function getSubtotal(): float
    {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return (float) $subtotal;
    }

    public function getItemCount(): int
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }
}
