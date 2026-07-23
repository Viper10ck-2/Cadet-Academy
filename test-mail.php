<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('Test email dari Cadet Academy via Brevo SMTP', function ($m) {
        $m->to('mohd.rizki08@gmail.com')->subject('Test SMTP Brevo');
    });
    echo "SUCCESS - Email sent!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
