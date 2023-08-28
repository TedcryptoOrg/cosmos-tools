<?php

namespace App\Model\CosmosDirectory\Chains\Parameters;

class SlashingParameters
{
    private string $signedBlocksWindow;

    private string $minSignedPerWindow;

    private string $downtimeJailDuration;

    private string $slashFractionDoubleSign;

    private string $slashFractionDowntime;

    public function getSignedBlocksWindow(): string
    {
        return $this->signedBlocksWindow;
    }

    public function setSignedBlocksWindow(string $signedBlocksWindow): SlashingParameters
    {
        $this->signedBlocksWindow = $signedBlocksWindow;

        return $this;
    }

    public function getMinSignedPerWindow(): string
    {
        return $this->minSignedPerWindow;
    }

    public function setMinSignedPerWindow(string $minSignedPerWindow): SlashingParameters
    {
        $this->minSignedPerWindow = $minSignedPerWindow;

        return $this;
    }

    public function getDowntimeJailDuration(): string
    {
        return $this->downtimeJailDuration;
    }

    public function setDowntimeJailDuration(string $downtimeJailDuration): SlashingParameters
    {
        $this->downtimeJailDuration = $downtimeJailDuration;

        return $this;
    }

    public function getSlashFractionDoubleSign(): string
    {
        return $this->slashFractionDoubleSign;
    }

    public function setSlashFractionDoubleSign(string $slashFractionDoubleSign): SlashingParameters
    {
        $this->slashFractionDoubleSign = $slashFractionDoubleSign;

        return $this;
    }

    public function getSlashFractionDowntime(): string
    {
        return $this->slashFractionDowntime;
    }

    public function setSlashFractionDowntime(string $slashFractionDowntime): SlashingParameters
    {
        $this->slashFractionDowntime = $slashFractionDowntime;

        return $this;
    }
}
