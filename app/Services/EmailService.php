<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;


class EmailService
{
    public function sendNotificationEmail(): bool
    {
        $response = Http::get('https://run.mocky.io/v3/4ce65eb0-2eda-4d76-8c98-8acd9cfd2d39');

        if ($response->successful()) {
            return true;
        } else {
           return false;
        }
    }

    public function AllowVerify(): bool
    {
        $response = Http::get('https://run.mocky.io/v3/f2fe9a2d-090f-4129-b9bf-70d283c97d5c');

        if ($response->successful()) {
            return true;
        } else {
           return false;
        }
    }
}
