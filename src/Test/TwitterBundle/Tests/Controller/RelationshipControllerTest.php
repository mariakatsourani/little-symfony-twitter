<?php

namespace Test\TwitterBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RelationshipControllerTest extends WebTestCase
{
    public function testFollow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/follow');
    }

    public function testUnfollow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/unfollow');
    }

}
