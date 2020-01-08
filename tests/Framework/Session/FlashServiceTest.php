<?php

namespace Tests\Framework\Session;

use PHPUnit\Framework\TestCase;
use Framework\Session\ArraySession;
use Framework\Session\FlashService;

class FlashServiceTest extends TestCase
{
    private $session;

    private $flashService;

    public function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flashService = new FlashService($this->session);
    }
    public function testDeleteFlashAfterGettinIt()
    {
        $this->flashService->success('Bravo');
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
    }
}
