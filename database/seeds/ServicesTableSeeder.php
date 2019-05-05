<?php

/*
 * This file is part of Glugox.
 *
 * (c) Glugox <glugox@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Service;


/**
 * Description of ServicesTableSeeder
 *
 * @author Ervin
 */
class ServicesTableSeeder extends Seeder {
    
  
    /**
     * Run the database seeding.
     *
     * @return void
     */
    public function run() 
    {
        Service::unguard();
        
        $keys = [
            ['key' => 'products', 'base_url' => 'http://localhost:5020/api'], 
            ['key' => 'accounts', 'base_url' => 'http://localhost:5060/v1/api']
        ];
        foreach ($keys as $s){
            $service = Service::create($s); 
        }
        Service::reguard();
    }
    
}
    