<?php

namespace Tests\Framework\Twig;

use PHPUnit\Framework\TestCase;
use Framework\Twig\TimeExtension;

class TimeExtensionTest extends TestCase
{
    private  $timeExtension;

    public function setUp(): void
    {
        $this->timeExtension = new TimeExtension;
    }
    public function testDateFormat()
    {
        $date = new \Datetime();
        $format = 'd/m/y H:i';
        $result = '<span class="timeago" datetime="' . $date->format(\Datetime::ISO8601) . '">' . $date->format($format) . '</span>';
        $this->assertEquals($result, $this->timeExtension->ago($date));
    }
}
