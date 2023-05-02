<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;


class EmailService
{
    public function sendNotificationEmail()
    {
        $response = Http::get('https://run.mocky.io/v3/4ce65eb0-2eda-4d76-8c98-8acd9cfd2d39');

        if ($response->successful()) {
            dd($response);
        } else {
            dd($response);
        }
    }
}
