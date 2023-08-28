<?php

namespace App\Tests\Integration\Service\Uploader;

use App\Service\Uploader\TedcryptoTransfer;
use App\Tests\Integration\BaseIntegrationTestCase;

class TedcryptoTransferTest extends BaseIntegrationTestCase
{
    private TedcryptoTransfer $tedcryptoTransfer;

    protected function setUp(): void
    {
        /** @var TedcryptoTransfer $tedcryptoTransfer */
        $tedcryptoTransfer = $this->getService(TedcryptoTransfer::class);

        $this->tedcryptoTransfer = $tedcryptoTransfer;
    }

    public function testUpload(): void
    {
        self::assertStringContainsString(
            'tedcrypto.io',
            $this->tedcryptoTransfer->upload(__DIR__.'/upload_directory', 'upload_test')
        );
    }
}
