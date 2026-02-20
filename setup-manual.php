<?php
/**
 * Manual setup script - run when artisan commands aren't available
 * Usage: php setup-manual.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "1. Generating APP_KEY...\n";
$key = 'base64:'.base64_encode(random_bytes(32));
$envFile = __DIR__.'/.env';
$env = file_get_contents($envFile);
$env = preg_replace('/^APP_KEY=.*/m', 'APP_KEY='.$key, $env);
file_put_contents($envFile, $env);
echo "   Done.\n";

echo "2. Creating storage link...\n";
$target = __DIR__.'/storage/app/public';
$link = __DIR__.'/public/storage';
if (!file_exists($link)) {
    symlink($target, $link);
    echo "   Done.\n";
} else {
    echo "   Already exists.\n";
}

echo "3. Running migrations...\n";
$migrator = $app->make('migrator');
$repository = $app->make('migration.repository');
$repository->createRepository();
$migrator->run([__DIR__.'/database/migrations']);
echo "   Done.\n";

echo "\nSetup complete!\n";
