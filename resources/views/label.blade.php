@php

    $labelData = \Tsung\NovaLabelCreator\Http\Controllers\PrintLabelController::generateLabels($data);

    $labelType = Tsung\NovaLabelCreator\Models\LabelType::find($data->label_type);

    $column = 1;

    $columnTwo = true;

    $column = 1;
    $labelCount = 1;

@endphp
    <!doctype html>
<html>
<head>
    <title>Label Creator</title>
    <meta charset="UTF-8" />
    <style>
        {{ $style }}
    </style>
</head>
<body class="antialiased">
    @foreach ($labelData as $label)
        @if ($column == 1)
            <div class="flex">
                {!! $label !!}
            <?php $column++ ?>
        @else
            @if ($column == $labelType->columns)
                    {!! $label !!}
                </div>
                {{-- jika sudah kolom terakhir maka reset column --}}
                <?php $column = 1 ?>
            @else
                {!! $label !!}
                <?php $column++ ?>
            @endif
        @endif

{{--        @if ($labelType->columns == 2)--}}
{{--            @if ($columnTwo)--}}
{{--                <div class="flex">--}}
{{--                    {!! $label !!}--}}
{{--            @else--}}
{{--                    {!! $label !!}--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            <?php $columnTwo = ! $columnTwo ?>--}}
{{--        @endif--}}

        @if ($labelType->columns >= 2 && $labelCount % $labelType->break_count == 0)
            <div style="page-break-after: always"></div>
        @endif

        <?php
            $labelCount++
        ?>
    @endforeach
</body>
</html>
