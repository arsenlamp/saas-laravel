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
use App\TicketsHaveTypes;

/**
 * Class TicketsHaveTypesTest
 *
 * Test for TicketsHaveTypes
 */
class TicketsHaveTypesTest extends TestCase
{
    public function testGetTicketType()
    {
        $result = TicketsHaveTypes::getTicketType(env('DATA_WORKSPACE'), env('DATA_TICKETTYPEEXISTING'));
        $this->assertIsObject($result);
        $this->assertEquals(env('DATA_TICKETTYPEEXISTING'), $result->id);
        $this->assertEquals(env('DATA_TICKETTYPEEXISTINGNAME'), $result->name);

        $result = TicketsHaveTypes::getTicketType(env('DATA_WORKSPACE'), env('DATA_TICKETTYPENONEXISTING'));
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(0, $result->id);
        $this->assertEquals(TicketsHaveTypes::UNKNOWN_TICKET_TYPE_IDENTIFIER, $result->name);
    }
}
