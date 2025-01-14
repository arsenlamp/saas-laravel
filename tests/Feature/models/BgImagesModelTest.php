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
use App\BgImagesModel;

/**
 * Class BgImagesModelTest
 * 
 * Test for BgImagesModel
 */
class BgImagesModelTest extends TestCase
{
    /**
     * Test for isValidImage
     *
     * @return void
     */
    public function testIsValidImage()
    {
        $result = BgImagesModel::isValidImage(public_path() . '/gfx/not_found.png');
        $this->assertFalse($result);

        $result = BgImagesModel::isValidImage(public_path() . '/gfx/header.png');
        $this->assertTrue($result);
    }

    /**
     * Test for getAllBackgrounds
     * 
     * @return void
     */
    public function testGetAllBackgrounds()
    {
        $result = BgImagesModel::getAllBackgrounds(env('DATA_WORKSPACE'));
        $this->assertTrue($result !== null);
        $this->assertTrue(isset($result[0]->workspace));
    }

    /**
     * Test for queryRandomImage
     * 
     * @return void
     */
    public function testQueryRandomImage()
    {
        $result = BgImagesModel::queryRandomImage(env('DATA_WORKSPACE'));
        $this->assertTrue($result !== null);
        $this->assertTrue(isset($result->workspace));
    }
}
