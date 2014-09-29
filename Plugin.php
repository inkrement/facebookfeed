<?php namespace Inkrement\FacebookFeed;

use App;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;

/**
 * FacebookFeed Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'FacebookFeed',
            'description' => 'Provides a Facebook feed import',
            'author'      => 'Inkrement',
            'icon'        => 'icon-leaf'
        ];
    }

	public function registerComponents(){
		return [
			'\Inkrement\FacebookFeed\Components\FacebookFeed' => 'facebookfeed'
		];
	}


     public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Facebook',
                'description' => 'Manage Facebook settings.',
                'category'    => 'Social Media',
                'class'       => 'Inkrement\FacebookFeed\Models\Settings',
                'order'       => 100
            ]
        ];
    }
}