<?php
namespace Tenko\Test;

require "../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;

class TestRequest extends TestCase
{
    public function testTimeout() {
        $this->expectException(Exception::class);
        Requests::get('https://github.com', [], ['timeout' => 1]);
    }
}
