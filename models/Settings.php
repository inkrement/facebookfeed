<?php 

namespace Inkrement\FacebookFeed\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'inkrement_facebookfeed_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}