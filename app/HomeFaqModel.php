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
 * Class HomeFaqModel
 * 
 * Represents the FAQ of the home
 */
class HomeFaqModel extends Model
{
    /**
     * Get all FAQ items
     */
    public static function getAll()
    {
        return HomeFaqModel::all();
    }
}
