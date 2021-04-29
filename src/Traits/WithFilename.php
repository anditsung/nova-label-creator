<?php

namespace Tsung\NovaLabelCreator\Traits;

trait WithFilename
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @return \Padocia\NovaPdf\Concerns\WithFilename
     */
    protected function handleFilename()
    {
        $this->filename = $this->filename();

        return $this;
    }

    /**
     * @return string
     */
    protected function filename()
    {
        return $this->defaultFilename();
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    protected function getFileExtension()
    {
        return 'pdf';
    }

    /**
     * @return string
     */
    abstract protected function defaultFilename(): string;



}
