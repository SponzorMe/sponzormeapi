<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class EnviromentTest extends TestCase
{
    /** @test  **/
    public function testEnviroment()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }
}
