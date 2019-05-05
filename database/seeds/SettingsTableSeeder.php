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
use App\Setting;


/**
 * Description of SettingsTableSeeder
 *
 * @author Ervin
 */
class SettingsTableSeeder extends Seeder {
    

        /**
         * Run the database seeding.
         *
         * @return void
         */
        public function run()
        {


            $settings = [
                [
                    'module'      => 'core',
                    'config_key'  => 'app_name',
                    'config_val'  => 'Glugate',
                    'data_type'   => 'string',
                    'label'       => 'App name',
                ],
                [
                    'module'      => 'core',
                    'config_key'  => 'items_per_page',
                    'config_val'  => '15',
                    'data_type'   => 'number',
                    'label'       => 'Items per page',
                ],
                [
                    'module'      => 'core',
                    'config_key'  => 'logo_img',
                    'config_val'  => '',
                    'data_type'   => 'image',
                    'label'       => 'Logo image',
                ],
                [
                    'module'      => 'ecommerce',
                    'config_key'  => 'enable_products',
                    'config_val'  => '1',
                    'data_type'   => 'boolean',
                    'label'       => 'Enable products',
                ],
                [
                    'module'      => 'layout',
                    'config_key'  => 'color_scheme',
                    'config_val'  => 'default',
                    'data_type'   => 'array',
                    'label'       => 'Color scheme',
                    'resource_url'=> '/api/layout/color_schemes'
                ],
                [
                    'module'      => 'activity',
                    'config_key'  => 'default_status',
                    'config_val'  => '',
                    'data_type'   => 'array',
                    'label'       => 'Default Activity Status',
                    'description' => 'When a new Activity is created, default status that is being set to that Activity',
                    'resource_url'=> '/api/activity/statuses'
                ]
            ];

            foreach ($settings as $settingItem){
                $settingModel = Setting::create($settingItem);
            }
        }
    
}
