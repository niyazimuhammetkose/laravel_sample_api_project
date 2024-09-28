<?php
declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateUsersIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('users', function (Mapping $mapping, Settings $settings) {
            $mapping->text('name');
//            $mapping->keyword('email');
            // email alanını hem text hem de keyword olarak ayarlamak için
            $mapping->text('email', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('users');
    }
}
