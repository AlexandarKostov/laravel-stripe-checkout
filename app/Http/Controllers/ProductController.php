<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        return view('product.index', [
            'products' => Product::all()
        ]);
    }

    public function checkout()
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $products = Product::all();
        $lineItems = [];
        $totalPrice = 0;
        foreach ($products as $product)
        {
            $totalPrice += $product->price;
            $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product->name,
                            'images' => [$product->image]
                        ],
                        'unit_amount' => $product->price * 100,
                    ],
                    'quantity' => 1,
            ];
        }

        $session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [] , true)."?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('checkout.cancel', [] , true),
        ]);

        $order = new Order();
        $order->status = 'unpaid';
        $order->total_price = $totalPrice;
        $order->session_id = $session->id;
        $order->save();

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $sessionId = $request->get('session_id');
        $customer = null;
        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if (!$session) {
                throw new NotFoundHttpException();
            }

            $customer = $stripe->customers->retrieve($session->customer);
            $order = Order::where('session_id', $session->id)->first();
            if (!$order)
            {
                throw new NotFoundHttpException();
            }
            if ($order->status === 'unpaid')
            {
                $order->status = 'paid';
                $order->save();
            }

            return \view('product.checkout-success', compact('customer'));
        }catch (\Exception $e)
        {
            throw new NotFoundHttpException();
        }
    }

//    public function cancel()
//    {
//        $order = Order::where('session_id', 'cs_test_b12SIX4k6VfqB60Lt4xzkUQBPAaF6RPg2VbZGAucfHlpqEbqcXlKvLor5l')->first();
//    }

    public function webhook()
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $endpoint_secret = env('WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('', 400);
        }

// Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $session = $event->data->object;
                $sessionId = $session->id;
                $order = Order::where('session_id', $session->id)->first();
                if ($order && $order->status === 'unpaid')
                {
                    $order->status = 'paid';
                    $order->save();
                    //here you can add something like send email to the customer
                }

            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('', 200);
    }
}
