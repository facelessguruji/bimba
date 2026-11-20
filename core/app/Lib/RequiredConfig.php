<?php

namespace App\Lib;

class RequiredConfig{

    public function getConfig(){
        return [
            'general_setting' => [
                'title'=>'Configure basic setting of your site like Site Name, Currency, Timezone etc',
                'route'=>route('admin.setting.general')
            ],
            'logo_favicon' => [
                'title'=>'Update the logo and favicon',
                'route'=>route('admin.setting.logo.icon')
            ],
            'notification_template' => [
                'title'=>'Update the global notification template',
                'route'=>route('admin.setting.notification.global.email')
            ],
            'deposit_method' => [
                'title'=>'Setup at-least one payment method',
                'route'=>route('admin.gateway.automatic.index')
            ],
            'category' => [
                'title'=>'Setup at-least one category',
                'route'=>route('admin.category.index')
            ],
            'country' => [
                'title'=>'Setup at-least one country',
                'route'=>route('admin.country.index')
            ],
            'city' => [
                'title'=>'Setup at-least one city',
                'route'=>route('admin.city.index')
            ],
            'plan' => [
                'title'=>'Setup at-least one plan',
                'route'=>route('admin.plan.index')
            ],
            'seo' => [
                'title'=>'Update the seo configuration',
                'route'=>route('admin.seo')
            ],
            'policy_content' => [
                'title'=>'Update the site policy content',
                'route'=>route('admin.frontend.sections','policy_pages')
            ],
        ];
    }

    public function totalConfigs(){
        return count($this->getConfig());
    }

    public function completedConfig(){
        // cast to array: the column is cast 'object', so a JSON object stored
        // in the DB (or garbage) would arrive as stdClass and crash count(),
        // in_array() and the admin dashboard that renders this component
        return (array) (gs('config_progress') ?? []);
    }

    public function completedConfigCount(){
        return count($this->completedConfig() ?? []);
    }

    public function completedConfigPercent(){
        $total = $this->totalConfigs();
        if ($total <= 0) {
            return 100;
        }
        return ($this->completedConfigCount() / $total) * 100;
    }

    public static function configured($key){
        $completedConfig = (array) (gs('config_progress') ?? []);
        if (!in_array($key,$completedConfig)) {
            $general = gs();
            $general->config_progress = array_merge($completedConfig,[$key]);
            $general->save();
        }
    }
}
