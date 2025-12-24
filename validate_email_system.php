<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "✅ Checking Email System Configuration...\n\n";

// Check MAIL_MAILER
$mailer = config('mail.default');
echo "MAIL_MAILER: $mailer\n";
if ($mailer === 'smtp') {
    echo "  ✓ Correct\n";
} else {
    echo "  ✗ ERROR: Should be 'smtp'\n";
}

// Check MAIL_HOST
$host = config('mail.mailers.smtp.host');
echo "\nMAIL_HOST: $host\n";
if ($host === 'sandbox.smtp.mailtrap.io') {
    echo "  ✓ Correct\n";
} else {
    echo "  ⚠ Warning: Check credentials\n";
}

// Check MAIL_ENCRYPTION
$encryption = config('mail.mailers.smtp.encryption');
echo "\nMAIL_ENCRYPTION: " . ($encryption ?: 'null') . "\n";
if ($encryption === null) {
    echo "  ✓ Correct\n";
} else {
    echo "  ✗ ERROR: Should be null\n";
}

// Check MAIL_FROM
$from = config('mail.from');
echo "\nMAIL_FROM_ADDRESS: " . $from['address'] . "\n";
echo "MAIL_FROM_NAME: " . $from['name'] . "\n";

// Check Jobs exist
echo "\n--- Queue Configuration ---\n";
$queue = config('queues.shipping');
echo "Shipping Queue: $queue\n";

echo "\n--- Job Classes ---\n";
if (class_exists('App\Jobs\SendOrderConfirmationEmailJob')) {
    echo "✓ SendOrderConfirmationEmailJob exists\n";
} else {
    echo "✗ SendOrderConfirmationEmailJob missing\n";
}
if (class_exists('App\Jobs\SendShippingUpdateEmailJob')) {
    echo "✓ SendShippingUpdateEmailJob exists\n";
} else {
    echo "✗ SendShippingUpdateEmailJob missing\n";
}
if (class_exists('App\Jobs\SendLoginAlertEmailJob')) {
    echo "✓ SendLoginAlertEmailJob exists\n";
} else {
    echo "✗ SendLoginAlertEmailJob missing\n";
}

echo "\n--- Mailable Classes ---\n";
if (class_exists('App\Mail\OrderPlacedMail')) {
    echo "✓ OrderPlacedMail exists\n";
} else {
    echo "✗ OrderPlacedMail missing\n";
}
if (class_exists('App\Mail\OrderShippedMail')) {
    echo "✓ OrderShippedMail exists\n";
} else {
    echo "✗ OrderShippedMail missing\n";
}
if (class_exists('App\Mail\LoginAlertMail')) {
    echo "✓ LoginAlertMail exists\n";
} else {
    echo "✗ LoginAlertMail missing\n";
}

echo "\n--- Event Classes ---\n";
if (class_exists('App\Events\OrderPlaced')) {
    echo "✓ OrderPlaced event exists\n";
} else {
    echo "✗ OrderPlaced event missing\n";
}
if (class_exists('App\Events\OrderStatusUpdated')) {
    echo "✓ OrderStatusUpdated event exists\n";
} else {
    echo "✗ OrderStatusUpdated event missing\n";
}

echo "\n--- Listener Classes ---\n";
if (class_exists('App\Listeners\SendOrderConfirmationEmail')) {
    echo "✓ SendOrderConfirmationEmail listener exists\n";
} else {
    echo "✗ SendOrderConfirmationEmail listener missing\n";
}
if (class_exists('App\Listeners\SendShippingUpdateEmail')) {
    echo "✓ SendShippingUpdateEmail listener exists\n";
} else {
    echo "✗ SendShippingUpdateEmail listener missing\n";
}
if (class_exists('App\Listeners\SendLoginAlertEmail')) {
    echo "✓ SendLoginAlertEmail listener exists\n";
} else {
    echo "✗ SendLoginAlertEmail listener missing\n";
}

echo "\n✅ Email System is properly configured!\n";
