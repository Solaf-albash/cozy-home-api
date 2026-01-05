<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- 1. تأكدي من استدعاء Schedule


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



 // جدولة أمر تحديث حالات الحجز ليعمل يومياً.

Schedule::command('bookings:update-status')->daily();
