<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = App\Models\DocumentTemplate::find(1);
if ($t) {
    $t->margin_top = 35;
    $t->margin_bottom = 12;
    $t->margin_left = 10;
    $t->margin_right = 10;
    $t->save();
    echo "Successfully updated template margins to: Top=35, Bottom=12, Left=10, Right=10\n";
} else {
    echo "Template not found.\n";
}
