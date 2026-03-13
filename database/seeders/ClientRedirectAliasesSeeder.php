<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientRedirectAliasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = \App\Models\Client::all();

        foreach ($clients as $client) {
            $urls = array_filter(explode(',', $client->redirect));
            
            foreach ($urls as $url) {
                \App\Models\ClientRedirectAlias::firstOrCreate([
                    'client_id' => $client->id,
                    'url' => trim($url),
                ], [
                    'alias' => null,
                    'icon' => null,
                ]);
            }
        }
    }
}
