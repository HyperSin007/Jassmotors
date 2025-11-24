<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:500',
            'business_city' => 'nullable|string|max:255',
            'business_phone' => 'nullable|string|max:50',
            'business_email' => 'nullable|email|max:255',
            'invoice_footer' => 'nullable|string|max:500',
            'invoice_footer_note' => 'nullable|string|max:500',
            'invoice_footer_contact' => 'nullable|string|max:500',
            'invoice_logo_width' => 'nullable|integer|min:30|max:200',
            'invoice_brand_font_size' => 'nullable|integer|min:16|max:48',
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:1024',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'site_logo' || $key === 'site_favicon') {
                continue; // Handle files separately
            }
            if ($value !== null) {
                Setting::set($key, $value);
            }
        }

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo')->store('logos', 'public');
            Setting::set('site_logo', $logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $faviconPath = $request->file('site_favicon')->store('logos', 'public');
            Setting::set('site_favicon', $faviconPath);
        }

        Setting::clearCache();

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully!');
    }
}
