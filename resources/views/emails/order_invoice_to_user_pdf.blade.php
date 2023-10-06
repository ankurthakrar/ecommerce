<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice PDF</title>

    <style>
        * {
            font-family: sans-serif;
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
        }

        .headSec {
            /* display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            column-gap: 30px; */
        }

        .imgBox {
            margin-top: 10px;
        }

        .rightHead {
            color: red;
            font-size: 22px;
            margin-top: 15px;
            font-weight: 600;
        }

        .leftHead {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            column-gap: 15px;
        }

        .leftHead .headText .headingg {
            color: red;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 3px;
        }

        .leftHead .headText .teadAdd {
            color: rgb(0, 0, 0);
            font-weight: 500;
            font-size: 12px;
        }

        .leftHead .headText .gstn {
            margin-top: 3px;
            color: rgb(0, 0, 0);
            font-weight: 600;
            font-size: 14px;
        }

        .inDateAndPlace {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .inDateAndPlace .entry {
            /* display: flex;
            align-items: center;
            justify-content: flex-start; */
            font-size: 12px;
            margin: 3px 0px;
        }

        .inDateAndPlace .entry .left {
            width: 160px;
        }

        .biltoShipTo {
            margin-top: 10px;
            background: #f5f5f5;
            /* display: flex;
            align-items: center;
            justify-content: space-between; */
            padding: 3px;
        }

        .biltoShipTo .left {
            font-size: 20px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .userAddressSec {
            /* display: flex;
            align-items: center;
            justify-content: space-between; */
            padding-top: 10px;
            border-bottom: 1px solid black;
            padding-bottom: 10px;
        }

        .userAddressSec .left .userName {
            font-weight: 600;
            font-size: 18px;
        }

        .userAddressSec .left .add {
            font-size: 14px;
            margin-top: 3px;
        }

        .userAddressSec .right {
            text-align: right;
        }

        .userAddressSec .right .add {
            font-size: 14px;
            margin-top: 3px;
        }

        .inHead {
            text-align: center;
            font-weight: 600;
            font-size: 22px;
            margin-top: 10px;
        }

        .bankDetMain{
            /* display: flex;
            align-items: center;
            justify-content: space-between; */
        }
        .bankDetMain .left .had{
            font-size: 13px;

        }
        .bankDetMain .right .titDiv{
            /* display: flex;
            align-items: center;
            justify-content: flex-end;
            column-gap: 10px; */
            font-size: 13px;

        }
        .bankDetMain .right .wordsDiv{
            display: flex;
            align-items: center;
            justify-content: flex-start;
            column-gap: 10px;
            font-size: 13px;

        }
        .rname{
            text-align: right;
            margin-top: 20px;
            font-size: 13px;
        }
        .lname{
            margin-top: 20px;
            font-size: 13px;
        }

    </style>

</head>

<body>

    <div class="parentDiv" style="width: 750px;height:920px;padding: 5px;padding-left:20px;">
        <div class="innerSec">
            <div>
                <table>
                    <tbody>
                        <tr>
                            <td> <div class="leftHead">
                                <div class="imgBox">
                                    <!-- <img src="{{url('')}}/public/images/hub-image.png" class="headImg" alt="logo"> -->
                                </div>
                                <div class="headText">
                                    <div class="headingg">
                                        Hub sports equipment pvt Ltd
                                    </div>
                                    <div class="teadAdd">
                                        Shop No. 115, Pristine Square Mall, Shankar Kalate Nagar, Wakad, Pune - 411057 Pune,
                                        Maharashtra India - 411057
                                    </div>
                                    <div class="teadAdd">
                                        M: +91 9175986669 | +91 8550994091 EMAIL: hubshootingsports@gmail.com WEBSITE:
                                        hubshootingsports@gmail.com
                                    </div>
                                    <div class="gstn">
                                        GSTN: 27ABCDE1234F2Z5
                                    </div>
            
                                </div>
                            </div></td>
                            <td>
                                <div class="rightHead">
                                    TAX <br>
                                    INVOICE
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
               
               
            </div>
            <div class="inDateAndPlace">
                <div class="entry">
                    <table>
                        <tbody>
                            <tr>
                                <td><div class="left">
                                    Invoice Date:
                                </div></td>
                                <td> <div class="right">
                                   {{ date('d/m/Y') }}
                                </div></td>
                            </tr>
                        </tbody>
                    </table>

                    
                   
                </div>
                <div class="entry">
                    <table>
                        <tbody>
                            <tr>
                                <td> <div class="left">
                                    Place of Supply :
                                </div></td>
                                <td> <div class="right">
                                  {{ $order['state_name']}}
                                </div></td>
                            </tr>
                        </tbody>
                    </table>
                   
                   
                </div>
            </div>

            <div class="biltoShipTo">
                <table style="width:100%;" >
                    <tbody>
                        <tr>
                            <td><div class="left">
                                Bill to
                            </div></td>
                            <td><div class="left" style="text-align: right;">
                                Ship to
                            </div></td>
                        </tr>
                    </tbody>
                </table>
                
                
            </div>

            <div class="userAddressSec">
                <table style="width:100%;">
                    <tbody>
                        <tr>
                            <td><div class="left">
                                <div class="userName">
                                   {{$order['full_name']}}
                                </div>
                                <div class="add">
                                   {{$order['address_line_1']}}{{$order['address_line_2']}},{{$order['city_name']}} , {{$order['state_name']}} - {{$order['pincode']}}
                                </div>
                                <div class="add">
                                    GSTN : URP
                                </div>
                            </div></td>
                            <td> <div class="right">
                                <div class="add">
                                    {{$order['address_line_1']}}{{$order['address_line_2']}},{{$order['city_name']}} , {{$order['state_name']}} - {{$order['pincode']}}
                                </div>
                                <div class="add">
                                    GSTN : URP
                                </div>
                            </div></td>
                        </tr>
                    </tbody>
                </table>
                
               
            </div>

            <div class="inHead">
                {{$order['invoice_num']}}
            </div>

            @if($order->has_accessory_false)
            <table style="font-family:arial,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                width="100%" border="0">
                <tbody>
                    <tr>
                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:arial,helvetica,sans-serif;"
                            align="left">

                            <div>
                                <div>
                                    <table style="width:100%;border:1px solid black; border-collapse:collapse;">
                                        <tr style="font-size:10px;border:1px solid black; border-collapse:collapse;">
                                            <th style="width:20px;border:1px solid black; border-collapse:collapse;">Sr.
                                            </th>
                                            <th style="border:1px solid black; border-collapse:collapse;">Item &
                                                Description</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">HSN/SAC</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">Quantity</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">Rate</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">Amount</th>
                                        </tr>
                                        <?php
                                            $total_qty = 0; 
                                            $total_item_price = 0.00;
                                        ?>
                                        @foreach($order->orderItems as $key=>$order1)
                                        @if(!$order1->is_accessory)
                                            <?php $total_qty = $total_qty + ($order1->qty) ?>
                                            <?php $total_item_price = $total_item_price + ($order1->final_item_price) ?>
                                        <tr
                                            style="text-align:center;font-size:10px;border:1px solid black; border-collapse:collapse;">
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$key+1}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->title}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">- </td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->qty}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">-</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->final_item_price}}
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                        <tr
                                            style="text-align:center;font-size:10px;border:1px solid black; border-collapse:collapse;">
                                            <td colspan="3" style="border:1px solid black; border-collapse:collapse;">
                                                Total</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$total_qty}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;"> </td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$total_item_price}}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
            @endif

            @if($order->has_accessory_true)
            <table style="font-family:arial,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                width="100%" border="0">
                <tbody>
                    <tr>
                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:arial,helvetica,sans-serif;"
                            align="left">

                            <div>
                                <div>
                                    <table style="width:100%;border:1px solid black; border-collapse:collapse;">
                                        <tr style="font-size:10px;border:1px solid black; border-collapse:collapse;">
                                            <th style="width:20px;border:1px solid black; border-collapse:collapse;">Sr.
                                            </th>
                                            <th style="border:1px solid black; border-collapse:collapse;">Item &
                                                Description</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">HSN/SAC</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">Quantity</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">Rate</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">TAXABLE
                                                VALUE</th>
                                            <th style="border:1px solid black; border-collapse:collapse;">CGST <br>
                                                (Value(%) </th>
                                            <th style="border:1px solid black; border-collapse:collapse;">SGST/UGST <br>
                                                (Value(%) </th>

                                            <th style="border:1px solid black; border-collapse:collapse;">Amount</th>
                                        </tr>
                                        <?php
                                            $total_qty = 0; 
                                            $total_item_price = 0.00;
                                        ?>
                                        @foreach($order->orderItems as $key=>$order1)
                                        @if($order1->is_accessory)
                                        <?php $total_qty = $total_qty + ($order1->qty) ?>
                                        <?php $total_item_price = $total_item_price + ($order1->final_item_price) ?>
                                        <tr
                                            style="text-align:center;font-size:10px;border:1px solid black; border-collapse:collapse;">
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$key+1}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->title}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">93040000 </td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->qty}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">-</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">-</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->cgst}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->sgst}}</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$order1->final_item_price}}
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                        <tr
                                            style="text-align:center;font-size:10px;border:1px solid black; border-collapse:collapse;">
                                            <td colspan="3" style="border:1px solid black; border-collapse:collapse;">
                                                Total</td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$total_qty}} </td>
                                            <td style="border:1px solid black; border-collapse:collapse;"> </td>

                                            <td style="border:1px solid black; border-collapse:collapse;"></td>
                                            <td style="border:1px solid black; border-collapse:collapse;"></td>
                                            <td style="border:1px solid black; border-collapse:collapse;"></td>
                                            <td style="border:1px solid black; border-collapse:collapse;">{{$total_item_price}}
                                            </td>


                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
            @endif

            <div class="bankDetMain">
                <table style="width: 100%;" >
                    <tbody>
                        <tr>
                            <td> <div class="left">
                                <div class="had">
                                    Bank Details:
                                </div>
                                <div class="had">
                                    Account Name: Zybra Private Limited
                                </div>
                                <div class="had">
                                    Bank Name: XXXXXX
                                </div>
                                <div class="had">
                                    Account Number: XXXXXXXX
                                </div>
                                <div class="had">
                                    IFSC: XXXXXXX
                                </div>
            
                            </div></td>
                            <td><div class="right">
                                <div class="titDiv">
                                    <table style="width: 100%;" >
                                        <tbody>
                                            <tr>
                                                <td><div class="l" style="text-align: right;width: 300px;" >
                                                    Sub Total:
                                                </div></td>
                                                <td> <div class="r" style="text-align: right;">
                                                    -
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="titDiv">
                                    <table style="width: 100%;" >
                                        <tbody>
                                            <tr>
                                                <td><div class="l" style="text-align: right;width: 300px;" >
                                                    Total:
                                                </div></td>
                                                <td> <div class="r" style="text-align: right;">
                                                   {{$order['total_amount']}}
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                             
                                <div class="wordsDiv">
                                    <div class="l">
                                        Total in words :
                                    </div>
                                    <div class="r">
                                        {{$order['total_in_words']}} only
                                    </div>
                                </div>
                            </div></td>
                        </tr>
                    </tbody>
                </table>
               
                
            </div>

            <div class="rname">
                For, Hub sports equipment pvt Ltd
            </div>
            <div class="lname">
                * This is a computer generated receipt.
            </div>


        </div>
    </div>

</body>

</html>