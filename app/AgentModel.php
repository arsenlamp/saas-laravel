<?php

/*
    HelpRealm (dnyHelpRealm) developed by Arsen

    (C) 2019 - 2024 by Arsen

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AgentModel
 * 
 * Represents agents
 */
class AgentModel extends Model
{
    protected $fillable = ['workspace', 'surname', 'lastname', 'email', 'position', 'superadmin', 'user_id'];

    /**
     * An agent can be assigned to multiple groups
     * 
     * @return void
     */
    public function group()
    {
        $this->hasMany('App\AgentsHaveGroups');
    }

    /**
     * Query agent data row
     * 
     * @return mixed
     */
    public static function queryAgent($id)
    {
        $agent = AgentModel::where('id', '=', $id)->first();
        
        return $agent;
    }

    /**
     * Check whether agent is a super admin
     * 
     * @return bool
     */
    public static function isSuperAdmin($id)
    {
        $agent = AgentModel::where('id', '=', $id)->first();
        if ($agent === null) {
            return false;
        }

        return $agent->superadmin;
    }
}
