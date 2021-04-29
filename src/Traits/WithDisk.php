<?php


namespace Tsung\NovaLabelCreator\Traits;


use Illuminate\Support\Facades\Storage;

trait WithDisk
{
    /**
     * @var string
     */
    protected $disk = "local";

    /**
     * @param string $disk
     *
     */
    public function withDisk(string $disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @return string
     */
    protected function getDisk()
    {
        return $this->disk;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getFilePathFromDisk($filename)
    {
        return Storage::disk($this->getDisk())->path($filename);
    }
}
