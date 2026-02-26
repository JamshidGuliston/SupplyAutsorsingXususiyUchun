<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yuk xati</title>
    <style>
        @page {
            margin: 0.2in 0.8in 0.2in 0.3in;
            size: A4 portrait;
        }

        body {
            font-family: DejaVu Sans;
            font-size: 10px;
            background-position: top left;
            background-repeat: no-repeat;
            background-size: 100%;
            width: 100%;
        }

        table {
            border-collapse: collapse;
            border: 2px solid black;
            width: 100%;
        }

        thead {
            border: 2px solid black;
        }

        td {
            text-align: center;
            width: auto;
            overflow: hidden;
            word-wrap: break-word;
        }

        th {
            border: 1px solid black;
            padding: 2px;
        }

        td {
            border-right: 1px dashed black;
            border-bottom: 1px solid black;
            padding: 1px;
        }

        .page-break {
            page-break-after: always;
        }

        .header-info {
            margin-bottom: 6px;
        }

        .column {
            float: left;
            text-align: center;
            width: 50%;
        }

        .row-footer:after {
            content: "";
            display: table;
            clear: both;
        }

        .row-footer {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    @php
        $yukXatiIndex = 1;
    @endphp

    @foreach($yukxatlar as $idx => $yukxat)
        @php
            $chunkDays = $yukxat['days'];
            $chunkProducts = $yukxat['products'];
            $fromDay = $chunkDays->first();
            $toDay = $chunkDays->last();
            $isLast = ($idx === count($yukxatlar) - 1);
        @endphp

        <div class="header-info">
            <center>
                <b>Юк хати &nbsp;&nbsp;&nbsp;&nbsp; № _____ &nbsp;&nbsp;&nbsp;&nbsp; Сана:
                    ____-____-{{ $fromDay->year_name ?? '' }}</b>
            </center>
            <b>{{ $kindgar->number_of_org ?? '' }}</b>
        </div>

        <table style="width:100%; table-layout: fixed;">
            <thead>
                <tr>
                    <th scope="col" style="width: 6%;">TR</th>
                    <th scope="col" style="width: 45%;">Mahsulotlar</th>
                    <th scope="col" style="width: 17%;">O'lcham</th>
                    <th scope="col" style="width: 17%;">Miqdori</th>
                    <th scope="col" style="width: 15%;">...</th>
                </tr>
            </thead>
            <tbody>
                @php $tr = 1; @endphp
                @foreach($chunkProducts as $product)
                    @if($product['product_name'] !== 'Болалар сони' && $product['total'] > 0)
                        <tr>
                            <th scope="row">{{ $tr++ }}</th>
                            <td style="text-align: left; padding-left: 3px;">{{ $product['product_name'] }}</td>
                            <td>{{ $product['size_name'] ?? '' }}</td>
                            <td><?php            printf("%01.2f", $product['total']); ?></td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="row-footer">
            <div class="column">
                @php
                    $qrImage = base64_encode(file_get_contents(public_path('images/qrmanzil.jpg')));
                @endphp
                <img src="data:image/jpeg;base64,{{ $qrImage }}" style="width:100; position:absolute; left:10px;">
            </div>
            <div class="column">
                <p style="text-align: right;">Қабул қилувчи: __________________;</p>
            </div>
        </div>

        @if(!$isLast)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>