<?php

namespace Tsung\NovaLabelCreator\Http\Controllers;


use App\Http\Controllers\Controller;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tsung\NovaLabelCreator\ExportToPdf;
use Tsung\NovaLabelCreator\Models\LabelType as LabelTypeModel;
use Tsung\NovaManufacture\Models\Plant;

class PrintLabelController extends Controller
{
    public static function generateLabels($data)
    {
        $labelType = LabelTypeModel::find($data->label_type);

        $plant = "";
        if(isset($data->plant)) {
            $plant = Plant::find($data->plant)->name;
        }

        $color = "";
        if(isset($data->color)) {
            $color = substr(config('novalabel.colors')[$data->color],0, 1);
        }

        $numbers = self::generatedNumber($data->number);

        $labels = [];

        foreach ($numbers as $number) {
            $label = self::updateDesign($labelType, $plant, $color, $number);
            for($i = 0; $i < $data->copy; $i++) {
                $labels[] = $label;
            }

        }

        return $labels;
    }

    public static function updateDesign($labelType, $plant, $color, $number)
    {
        $design = $labelType->design;

        foreach ($labelType->attributes as $name => $attribute) {
            $key = "[" . strtoupper($name) . "]";
            switch ($name)
            {
                case 'plant':
                    $replace = $plant;
                    break;

                case 'color':
                    $replace = $color;
                    break;

                case 'barcode':
                    $barcode = [
                        'plan' => $plant,
                        'color' => $color,
                        'number' => $number
                    ];
                    $replace = QrCode::size(250)
                        ->errorCorrection('h')
                        ->generate(base64_encode(json_encode($barcode)));
                    break;
            }
            $design = str_replace($key, $replace, $design);
        }

        $numberPad = str_pad($number, $labelType->number_digits, "0", STR_PAD_LEFT);
        $design = str_replace("[NUMBER]", $numberPad, $design);

        return $design;
    }

    public static function generatedNumber($number_string)
    {
        $numbers = array();

        $number_list = explode(",", $number_string);

        foreach($number_list as $number) {

            if(strpos($number, '~') !== false) {

                $number_range = explode('~', $number);

                $numbers = array_merge($numbers, range($number_range[0], $number_range[1]));

            }

            else {

                $number = array(intval($number));

                $numbers = array_merge($numbers, $number);

            }

        }

        return $numbers;
    }

    private function label_types()
    {
        return LabelTypeModel::all()->pluck('name', 'id');
    }

    private function plants()
    {
        return Plant::all()->pluck('name', 'id');
    }

    private function colors()
    {
        return config('novalabel.colors');
    }

    private function prepareFields($attributes)
    {
        $fields = [];
        foreach($attributes as $key => $value) {
            switch($key)
            {
//                case 'barcode':
//                    if($value) {
//                        $fields[] = Boolean::make('Barcode')->rules('required');
//                    }
//                    break;

                case 'plant':
                    if($value) {
                        $fields[] = Select::make('Plant')
                            ->options($this->plants())
                            ->required();
                    }
                    break;

                case 'color':
                    if($value) {
                        $fields[] = Select::make('Color')
                            ->options($this->colors())
                            ->required();
                    }
                    break;
            }
        }
        $fields[] = Text::make('Number')
            ->required()
            ->help("Example:<br>1~10<br>1,3,5,11");
//        $fields[] = Number::make('Start')->rules('required');
//        $fields[] = Number::make('End')->rules('required');
        $fields[] = Number::make('Copy')
            ->default(2)
            ->required();

        return $fields;
    }

    private function dependFields()
    {
        $dependsField = [];

        foreach(LabelTypeModel::all() as $label_type) {

            $fields = $this->prepareFields($label_type->attributes);

            $dependsField[] = NovaDependencyContainer::make($fields)->dependsOn('label_type', $label_type->id);
        }

        return $dependsField;
    }

    public function fields(NovaRequest $request)
    {
        $fields = [
            Select::make('Label Type')
                ->options($this->label_types()),
        ];

        $fields = array_merge($fields, $this->dependFields());

        return $fields;
    }

    public function printLabel(NovaRequest $request)
    {
        $this->validateData($request);

        return $this->prepareData($request);
    }

    private function prepareData(NovaRequest $request)
    {
        $data = collect($request->all())->map(function($value, $key) {
            return $value;
        });

        return base64_encode(json_encode($data));
    }

    private function validateData(NovaRequest $request)
    {
        // cek number yang di input adalah angka
        $rules = collect($request->all())->map(function($value, $key) {
            return [$key => 'required'];
        })->toArray();

        Validator::make($request->all(), $rules)->validate();
    }

    public function labels(NovaRequest $request)
    {
        $request->validate([
            'label_type' => 'required',
            'plant' => 'required|exists:manufacture_plants,id',
            'color' => 'required',
            'number' => 'required',
            'copy' => 'required|numeric'
        ]);

        $export = new ExportToPdf('label-creator::label', $request);

        return Action::download($export->getDownloadUrl(), $export->getFilename());
    }

    /**
     * @param Request         $request
     * @param ResponseFactory $response
     *
     * @return BinaryFileResponse
     * @throws ValidationException
     */
    public function download(Request $request, ResponseFactory $response): BinaryFileResponse
    {
        $data = $this->validate($request, [
            'path'     => 'required',
            'filename' => 'required',
        ]);

        return $response->download(
            $data['path'],
            $data['filename'],
            ['Content-Type: application/pdf']
        )->deleteFileAfterSend($shouldDelete = true);
    }
}
