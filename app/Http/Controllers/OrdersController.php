<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\Orders;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::all();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user_id' => 'required|exists:users,_id',
            'menu_item_id' => 'required|array',
            'menu_item_id.*' => 'exists:menu,_id', // Validate each menu item ID in the array
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1', // Validate each quantity in the array
        ]);

        // Initialize arrays to store order details
        $menu_item_ids = [];
        $quantities = [];

        // Calculate total price and create orders for each menu item
        foreach ($request->menu_item_id as $key => $menuItemID) {
            // Find the menu item to get its price
            $menuItem = Menu::findOrFail($menuItemID);

            // Add menu item ID and quantity to respective arrays
            $menu_item_ids[] = $menuItemID;
            $quantities[] = $request->quantity[$key];
        }

        // Create the order
        $order = Orders::create([
            'user_id' => $request->user_id,
            'menu_item_id' => $menu_item_ids,
            'quantity' => $quantities,
            'price_total' => 0, // Total price will be calculated later
            'status' => 'Cooking', // Set default status to "Cooking"
            'created_at' => now(), // Set current timestamp for created_at
        ]);

        // Calculate total price
        $totalPrice = 0;
        foreach ($order->menu_item_id as $key => $menuItemID) {
            $menuItem = Menu::findOrFail($menuItemID);
            $totalPrice += $menuItem->price * $order->quantity[$key];
        }

        // Update the order with total price
        $order->update(['price_total' => $totalPrice]);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    public function show($id)
    {
        $order = Orders::findOrFail($id);
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'status' => 'sometimes|required|string|in:Cooking,Completed,Canceled'
        ]);

        // Find the order
        $order = Orders::findOrFail($id);

        // Update the order
        $order->update([
            'quantity' => $request->quantity,
            'price_total' => $order->menu_item->price * $request->quantity, // Recalculate price_total
            'status' => $request->status ?? $order->status, // Update status if provided
        ]);

        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }

    public function destroy($id)
    {
        $order = Orders::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
