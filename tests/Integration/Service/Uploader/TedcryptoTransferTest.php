<?php

namespace App\Tests\Integration\Service\Uploader;

use App\Service\Uploader\TedcryptoTransfer;
use App\Tests\Integration\BaseIntegrationTestCase;
use PHPUnit\Framework\TestCase;

class TedcryptoTransferTest extends BaseIntegrationTestCase
{
    private TedcryptoTransfer $tedcryptoTransfer;

    protected function setUp(): void
    {
        $this->tedcryptoTransfer = $this->getService(TedcryptoTransfer::class);
    }

    public function testUpload()
    {
        $this->assertStringContainsString(
            'tedcrypto.io',
            $this->tedcryptoTransfer->upload(__DIR__ . '/upload_directory', 'upload_test')
        );
    }
}
