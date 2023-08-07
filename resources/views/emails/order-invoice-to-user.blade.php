<html>

<body>
    <div>
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            .order_table td,
            .order_table th {
                border: 1px solid #D1D1D1;
            }
        </style>
        <div class="" style='padding: 0 75px;'>
            <div style="background: #F7F7F7;width: 800px;margin: 0 auto 50px;padding: 50px;">
                <table style="background: #ffffff;margin: 0 auto;width: 100%;border-radius:3px 3px 0 0!important;color:#ffffff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-bottom: 1px solid #d1d1d1;">
                    <tbody>
                        <tr>
                            <td style="padding: 36px 22px;">
                            <img src="{{ URL::asset('public/images/Kasturveda.png')}}">
                            </td>
                            <td id="m_-6338399178495416616header_wrapper"></td>
                        </tr>
                    </tbody>
                </table>
                <!-- header end -->
                <table style='width: 100%;background: #ffffff;margin: 0 auto;'>
                    <tr>
                        <td>
                            <h1 style="color: #69616A;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:22px;font-weight:300;line-height:28px;margin:0;padding: 30px 22px;">Thank you for your order</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="padding: 40px 20px 0px;color: #5B5B5B;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;">Your order has been received and is now being processed. Your order details are shown below for your reference:</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="padding: 40px 20px 0px;color: #5B5B5B;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;margin-bottom:5px;">Order Number: <span style="color: #929292;font-weight: 500;">{{$order['order_id']}}</span></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="padding: 0px 20px 20px;color: #5B5B5B;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;margin-top:0px;">Date & Time: <span style="color: #929292;font-weight: 500;">
                                    <?php
                                    echo date('d-m-Y h:i:s a', strtotime($order['created_at'])); ?></span></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 22px;">
                            <hr style='margin: 20px 0; border-top: 1px solid #D1D1D1;'>
                        </td>
                    </tr>
                </table>

                <table style='width: 100%;background: #ffffff;margin: 0 auto;'>
                    <tbody>
                        <tr>
                            <th style="width: 50%;">
                                <h2 style="padding-left: 22px;color: #000000;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left">Delivered To Address</h2>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <p style="padding-left: 22px;width: 70%; color: #000000;line-height: 26px;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:16px;">
                                    <span style="display: block;">{{$order['first_name']}} {{$order['last_name']}}</span>
                                    {{$order['address_line_1']}}, {{$order['address_line_2']}}, {{$order['city']}}, {{$order['state_name']}},{{$order['zipcode']}}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table style='width: 100%;margin: 0 auto;background: #edebeb;border-collapse:collapse; padding: 0 22px;'>
                    <tbody>
                        <tr>
                            <th style="border: 1px solid #D1D1D1;width: 40%; text-align:left;color: #69616A;font-size: 14px;padding:12px">Title</th>
                            <th style="border: 1px solid #D1D1D1;width: 40%; text-align:left;color: #69616A;font-size: 14px;padding:12px">Qty</th>
                            <th style="border: 1px solid #D1D1D1;width: 20%; text-align:left;color: #69616A;font-size: 14px;padding:12px">Amount (Rs.)</th>
                        </tr>
                        @foreach($order['order_items'] as $order_item)
                        <tr>
                            <td style="border: 1px solid #D1D1D1;">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px">{{$order_item['title']}}</h2>
                            </td>
                            <td style="border: 1px solid #D1D1D1;">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px">{{$order_item['qty']}}</h2>
                            </td> 
                            <td style="border: 1px solid #D1D1D1;">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px">{{ round($order_item['final_price'])}}</h2>
                            </td>
                        </tr>
                        @endforeach
                        <!-- <tr>
                            <td style="border:0;border-left: 1px solid #D1D1D1;"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border: 1px solid #D1D1D1;" colspan="2">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;">Gross Amount</h2>
                            </td>
                            <td style="border: 1px solid #D1D1D1;" style="border: 1px solid #D1D1D1;">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;"></h2>
                            </td>
                        </tr> -->
                        <!-- <tr>
                            <td style="border:0;border-left: 1px solid #D1D1D1;"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border: 1px solid #D1D1D1;" colspan="2">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;">Tax Amount</h2>
                            </td>
                            @php
                            @endphp
                            <td style="border: 1px solid #D1D1D1;">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;"></h2>
                            </td>
                        </tr> -->
                        <!-- <tr>
                            <td style="border:0;border-left: 1px solid #D1D1D1;"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border: 1px solid #D1D1D1;" colspan="2">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;">Delivery Charge</h2>
                            </td>
                            <td style="border: 1px solid #D1D1D1;">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;"></h2>
                            </td>
                        </tr> -->
                        <!-- <tr>
                            <td style="border:0;border-left: 1px solid #D1D1D1;"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border:0"></td>
                            <td style="border: 1px solid #D1D1D1;" colspan="2">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;">Discount</h2>
                            </td>
                            <td style="border: 1px solid #D1D1D1;">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;"></h2>
                            </td>
                        </tr> -->
                        <tr>
                            <td style="border: 1px solid #D1D1D1;" colspan="1">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;">Payment Method</h2>
                            </td>
                            <td style="border: 1px solid #D1D1D1;" colspan="2">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px"><span style="color: #5B5B5B;">{{$order['payment_method']}}</span></h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #D1D1D1;" colspan="1">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px">Total Amount</h2>
                            </td>
                            <td style="border: 1px solid #D1D1D1;" colspan="2">
                                <h2 style="text-align:left;vertical-align:middle;font-size: 14px;line-height: 16px;word-wrap:break-word;color: #000000;padding:12px;">{{$order['total_amount']}}</h2>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Footer start -->
                <table border="0" cellpadding="10" style="margin: 0 auto 50px;width: 100%;background: #ffffff;" cellspacing="0">
                    <tbody style="padding-top: 50px;">
                        <tr>
                            <td style="padding: 22px;">
                                <h1 style="color: #5B5B5B;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:300;line-height:19px;margin:0;">Thank you, <br> <span style="padding-top: 6px;display: block;">Team Ecommerce</span></h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 22px 22px 5px;">
                                <h1 style="padding-top: 20px;color: #5B5B5B;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:300;line-height:19px;margin:0;">For any order related query please feel free to get in touch with us</h1>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h1 style="padding-bottom: 50px;color: #5B5B5B;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:300;line-height:19px;margin:0;"><img style='padding-right: 8px;' src="" alt=""> admin@gmail.com <span style='padding-left: 10px;'><img style='padding-right: 8px;' src="" alt=""></span></h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 22px;">
                                <p style='padding-top: 50px;color:#69616A;display:block;font-family:"Helvetica Neue",Helvetica,Roboto,Arial,sans-serif;font-size:15px;font-weight:500;line-height:130%;margin: 0 0 8px;text-align: center;'>Ecommerce</p>
                                <p style="border-top: 1px solid #D1D1D1;margin-bottom: 14px;"></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--footer end -->
            </div>
        </div>
</body>

</html>