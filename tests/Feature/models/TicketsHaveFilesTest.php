<?php

/*
    HelpRealm (dnyHelpRealm) developed by Arsen

    (C) 2019 - 2024 by Arsen

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\TicketsHaveFiles;

/**
 * Class TicketsHaveFilesTest
 *
 * Test for TicketsHaveFiles
 */
class TicketsHaveFilesTest extends TestCase
{
    /**
     * Test for getFileSize
     *
     * @return void
     */
    public function testGetFileSize()
    {
        $this->markTestSkipped();
        $result = TicketsHaveFiles::getFileSize(env('DATA_TICKETFILE'));
        $this->assertTrue($result > 0);
    }
}
