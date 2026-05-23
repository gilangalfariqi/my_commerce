<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Midtrans\MidtransWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected MidtransWebhookService $webhookService;

    public function __construct(MidtransWebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            $this->webhookService->handle($request->all());
            return response()->json(['status' => 'success', 'message' => 'Webhook handled successfully.']);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Controller error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
