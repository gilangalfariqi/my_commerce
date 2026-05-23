<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\RajaOngkir\RajaOngkirService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class SettingController extends Controller
{
    protected RajaOngkirService $rajaOngkirService;

    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
    }

    public function index(): View
    {
        $settings = Setting::all()->pluck('value', 'key');
        $provinces = $this->rajaOngkirService->getProvinces();
        
        $selectedProvinceId = $settings->get('rajaongkir_origin_province_id');
        $cities = $selectedProvinceId ? $this->rajaOngkirService->getCities($selectedProvinceId) : [];

        return view('admin.settings.index', compact('settings', 'provinces', 'cities'));
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'store_name' => 'required|string|max:255',
            'store_email' => 'required|email|max:255',
            'store_phone' => 'required|string|max:50',
            'store_address' => 'required|string',
            'rajaongkir_origin_province_id' => 'required|integer',
            'rajaongkir_origin_city_id' => 'required|integer',
            'midtrans_merchant_id' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_server_key' => 'nullable|string',
            'midtrans_is_production' => 'nullable|boolean',
        ];

        $request->validate($rules);

        Setting::setValue('store_name', $request->store_name, 'Name of the store');
        Setting::setValue('store_email', $request->store_email, 'Contact email');
        Setting::setValue('store_phone', $request->store_phone, 'Contact phone number');
        Setting::setValue('store_address', $request->store_address, 'Store street address');
        Setting::setValue('rajaongkir_origin_province_id', $request->rajaongkir_origin_province_id, 'RajaOngkir origin province ID');
        Setting::setValue('rajaongkir_origin_city_id', $request->rajaongkir_origin_city_id, 'RajaOngkir origin city ID');
        Setting::setValue('midtrans_merchant_id', $request->midtrans_merchant_id, 'Midtrans Merchant ID');
        Setting::setValue('midtrans_client_key', $request->midtrans_client_key, 'Midtrans Client Key');
        Setting::setValue('midtrans_server_key', $request->midtrans_server_key, 'Midtrans Server Key');
        Setting::setValue('midtrans_is_production', $request->has('midtrans_is_production') ? '1' : '0', 'Midtrans environment mode');

        return redirect()->route('admin.settings.index')->with('success', 'Store settings updated successfully.');
    }
}
