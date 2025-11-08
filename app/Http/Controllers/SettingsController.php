<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
   public function background(Request $request)
{
    $validated = $request->validate([
        'background_url' => 'required|string',
    ]);

    Settings::updateOrCreate(['id' => 1], $validated);

    return response("
        <script>
            window.top.location.href = '/';
        </script>
    ");

}


}
