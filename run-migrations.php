<?php
/**
 * Run migrations without artisan migrate command
 * Usage: php run-migrations.php
 */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$migrator = $app->make('migrator');
$paths = [__DIR__.'/database/migrations'];
$migrator->run($paths);
echo "Migrations complete.\n";
