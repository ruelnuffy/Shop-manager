<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index(): View
    {
        $items = $this->cartService->getItems();
        $subtotal = $this->cartService->getSubtotal();
        $shippingFee = $subtotal > 0 ? 3500.00 : 0.00;
        $total = $subtotal + $shippingFee;

        return view('store.cart.index', compact('items', 'subtotal', 'shippingFee', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            $quantity = (int) ($request->quantity ?? 1);
            $item = $this->cartService->add((int) $request->variant_id, $quantity);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Added to your shopping bag.',
                    'item' => $item,
                    'cart_count' => $this->cartService->getItemCount(),
                    'subtotal' => $this->cartService->getSubtotal(),
                ]);
            }

            if ($request->boolean('buy_now')) {
                return redirect()->route('checkout.index');
            }

            return redirect()->route('cart.index')->with('success', 'Item added to your shopping bag.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, int $variantId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $this->cartService->update($variantId, (int) $request->quantity);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => $this->cartService->getItemCount(),
                'subtotal' => $this->cartService->getSubtotal(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

    public function destroy(int $variantId): RedirectResponse
    {
        $this->cartService->remove($variantId);

        return redirect()->route('cart.index')->with('success', 'Item removed from your bag.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return redirect()->route('cart.index')->with('success', 'Your bag has been emptied.');
    }
}
