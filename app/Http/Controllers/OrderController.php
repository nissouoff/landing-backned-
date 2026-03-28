<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Landing;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $landing = Landing::where('slug', $slug)->first();
        
        if (!$landing || !$landing->is_published) {
            return response()->json(['message' => 'Boutique non disponible'], 404);
        }

        $validated = $request->validate([
            'product_id' => 'required|string',
            'product_name' => 'required|string',
            'product_price' => 'required|string',
            'product_photo' => 'nullable|string',
            'customer_name' => 'required|string|max:255',
            'customer_firstname' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:10|max:20',
            'wilaya' => 'required|string',
            'commune' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'delivery_type' => 'required|in:home,pickup',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::create([
            'landing_id' => $landing->id,
            'product_id' => $validated['product_id'],
            'product_name' => $validated['product_name'],
            'product_price' => $validated['product_price'],
            'product_photo' => $validated['product_photo'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_firstname' => $validated['customer_firstname'],
            'customer_phone' => $validated['customer_phone'],
            'wilaya' => $validated['wilaya'],
            'commune' => $validated['commune'] ?? null,
            'address' => $validated['address'] ?? null,
            'delivery_type' => $validated['delivery_type'],
            'is_verified' => true,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'Commande envoyée avec succès !',
            'order' => [
                'id' => $order->id,
                'product_name' => $order->product_name,
                'customer_name' => $order->customer_name . ' ' . $order->customer_firstname,
                'wilaya' => $order->wilaya,
                'delivery_type' => $order->delivery_type,
            ],
        ], 201);
    }

    public function index(Request $request)
    {
        $orders = $request->user()->landings()
            ->with(['orders' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->pluck('orders')
            ->flatten();

        return response()->json(['orders' => $orders]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->landing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Statut mis à jour',
            'order' => $order,
        ]);
    }
}
