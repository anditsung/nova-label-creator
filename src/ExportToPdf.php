<?php


namespace Tsung\NovaLabelCreator;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use Tsung\NovaLabelCreator\Traits\WithDisk;
use Tsung\NovaLabelCreator\Traits\WithFilename;

class ExportToPdf
{
    use WithFilename, WithDisk;

    /**
     * @var \Spatie\Browsershot\Browsershot
     */
    protected $browsershot;

    /**
     * @var int
     */
    protected $downloadUrlExpirationTime = 1;

    protected $preview;

    protected $filename;

    public function __construct($view, $request)
    {
        $this->browsershot = new Browsershot();

        // generate filename
        $this->handleFilename();

        $this->handle($view, $request);
    }

    public function handle($view, $request)
    {
        $this->preview = view($view, [
            'data' => $request,
            'style' => $this->getNovaWebStyle(),
        ]);

        $this->saveAsPdf();

        return $this;
    }

    public function saveAsPdf()
    {
        $pdfFileContent = $this->browsershot
            ->html($this->preview->render())
            ->format('legal') // F4
            ->pdf();

        Storage::disk($this->getDisk())->put($this->getFilename(), $pdfFileContent);

        return $this;
    }

    /**
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return URL::temporarySignedRoute('label-creator.download', now()->addMinutes($this->downloadUrlExpirationTime), [
            'path'     => $this->getFilePathFromDisk($this->getFilename()),
            'filename' => $this->getFilename(),
        ]);
    }

    protected function defaultFilename(): string
    {
        return 'generated-label-' . now()->timestamp . "." . $this->getFileExtension();
    }

    public function getNovaWebStyle()
    {
        $css = public_path('/vendor/novaweb/app.css');

        return file_get_contents($css);
    }
}
