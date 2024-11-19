<?php

namespace App\Models;
use CodeIgniter\Model;
class SettingsModel extends Model
{
    
    
    protected $table                = 'settings';
    protected $primaryKey           = 'id';
    protected $allowedFields        = ['class','key', 'value', 'type', 'context'];


    function get_setting($key) {
        $db      = \Config\Database::connect();
        $db_builder = $db->table('settings');
        $result = $db_builder->getWhere(array('key' => $key), 1);
        if (count($result->getResult()) == 1) {
            return $result->getRow()->value;
        }
    }



    function save_setting($key, $value) {
        $db      = \Config\Database::connect();
        $db_builder = $db->table('settings');
        $fields = array(
            'class'=> "Config\App",
            'key' => $key,
            'value' => $value,
            'type' => "string",
            'created_at' => date("Y-m-d h:i:s"),
            'updated_at' => date("Y-m-d h:i:s"),
        );

        $exists = $this->get_setting($key);
        if ($exists === NULL) {
            $fields["type"] = $type; //type can't be updated
            return $db_builder->insert($fields);
        
          } else{  
            $db_builder->where('key', $key);
            $db_builder->update($fields);
        
    }
}



}