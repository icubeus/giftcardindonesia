<?php
/**
 * Giftcard module API helper
 */
class Icube_GiftCard_Helper_Api_Data extends Mage_Core_Helper_Abstract
{
    const API_USERNAME_CONFIG_PATH              = 'giftcard/api_config/username';
    const API_PASSWORD_CONFIG_PATH              = 'giftcard/api_config/password';
    const API_KEY_CONFIG_PATH                    = 'giftcard/api_config/api_key';
    const API_SECRET_CONFIG_PATH              = 'giftcard/api_config/secret_key';
    const TOKEN_URL_CONFIG_PATH                 = 'giftcard/api_config/token_url';              // http://private-2109c-gcidistribution.apiary-mock.com/oauth/token
    const ACTIVATE_URL_CONFIG_PATH              = 'giftcard/api_config/activate_url';           // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards
    const REDEEM_URL_CONFIG_PATH                = 'giftcard/api_config/redeem_url';             // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/redeem
    const TOPUP_URL_CONFIG_PATH                 = 'giftcard/api_config/topup_url';              // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/topup
    const BALANCE_URL_CONFIG_PATH               = 'giftcard/api_config/balance_url';            // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/balance
    const REDEEM_FULL_URL_CONFIG_PATH           = 'giftcard/api_config/redeem_full_url';        // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/redeem_full
    const BALANCE_HOLD_URL_CONFIG_PATH          = 'giftcard/api_config/balance_hold_url';       // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/balance/hold
    const BALANCE_RESOLVE_URL_CONFIG_PATH       = 'giftcard/api_config/balance_resolve_url';    // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/balance/resolve
    const TRANSACTIONS_LIST_URL_CONFIG_PATH     = 'giftcard/api_config/transactions_list_url';  // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/transactions
    const TRANSACTION_URL_CONFIG_PATH           = 'giftcard/api_config/transaction_url';        // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/transactions/{id}
    const TRANSACTION_VOID_URL_CONFIG_PATH      = 'giftcard/api_config/transaction_void_url';   // http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/transactions/void
    const TRANSACTION_RECEIPT_URL_CONFIG_PATH   = 'giftcard/api_config/transaction_receipt_url';// http://private-2109c-gcidistribution.apiary-mock.com/v1/giftcards/transactions/{id}/email_receipt

    public function getToken()
    {
        $username = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::API_USERNAME_CONFIG_PATH);
        $password = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::API_PASSWORD_CONFIG_PATH);
        $apiKey = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::API_KEY_CONFIG_PATH);
        $secretKey = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::API_SECRET_CONFIG_PATH);
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::TOKEN_URL_CONFIG_PATH);

        $data = array(
            'grant_type'    =>'password',
            'scope'         =>'offline_access',
            'username'      => $username,
            'password'      => $password,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey.':'.$secretKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded",
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        $access_token = $response->access_token;
        return $access_token;
        /*
         * 200
        Content-Type: application/json
        {
          "access_token": "Unique distributor store token id",
          "refresh_token": "refresh token used to get new access token if the old access token is expired",
          "expires_in": 3600,
          "token_type": "Bearer"
        }
        */
    }

    public function activate($distributionId,$amount)
    {
        $token = $this->getToken();
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::ACTIVATE_URL_CONFIG_PATH);
        $data = array(
            "distributionId"    => $distributionId,
            "amount"            => $amount,
        );
        $data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$token
        ));
        $response = curl_exec($ch);
        Mage::log('orig $response:'.print_r($response,true),null,'GCdata.log',true);
        curl_close($ch);
        $response = '{"cardNo":"6817133197574717","balance":'.$amount.',"expired":"03/09/2015","status":"active","approvalCode":788590,"merchant":"Giftcard4Dummies","trxNo":"307382612489163","trxType":"activation","trxCode":"6934657e57a072d4dacc2ad074d4edba1cde8cd7ab707508ab47030cf7b27b542d7954de9f1d2e8b","trxStatus":"accepted","trxAmount":30000,"trxTime":"04/08/2015","terminalId":166,"id":5469}';
        $response = json_decode($response);
        return $response;
        /*
         * 200
        Content-Type: application/json
        {
          "cardNo": "12312312312312",
          "balance": "100000",
          "expired": "01/01/2017",
          "status": "active",
          "merchant": "Starbucks Coffee",
          "approvalCode": "123456",
          "trxNo": "1234567890",
          "trxType": "activation",
          "trxAmount": "250000",
          "trxCode": "1234567890123456123213123",
          "trxStatus": "accepted",
          "trxTime": "2015-03-06T04:39:33.000Z",
          "terminalId": "1",
          "id": "1"
        }

        400
        Content-Type: application/json
        "err": "Error message"

        403
        Content-Type: application/json
        "Unauthorized"
         * */

    }

    public function redeem($distributionId,$amount)
    {
        $token = $this->getToken();
//        $pin = 'pin';
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::REDEEM_URL_CONFIG_PATH);
        $data = array(
            'distributionId'    => $distributionId,
//            'pin'               => $pin,
            'amount'            => $amount,
        );
        $data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$token
        ));

        $response = curl_exec($ch);
        Mage::log('orig $response:'.print_r($response,true),null,'GCdata.log',true);
        curl_close($ch);
        $response = json_decode($response);
        return $response;
        /*
         * 200
        Content-Type: application/json
        {
          "cardNo": "12312312312312",
          "balance": "100000",
          "expired": "01/01/2017",
          "status": "active",
          "merchant": "Starbucks Coffee",
          "approvalCode": "123456",
          "trxNo": "1234567890",
          "trxType": "redeem",
          "trxAmount": "250000",
          "trxCode": "1234567890123456123213123",
          "trxStatus": "accepted",
          "trxTime": "2015-03-06T04:39:33.000Z",
          "terminalId": "1",
          "id": "1"
        }

        400
        Content-Type: application/json
        "err": "Error message"

        403
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function topup($distributionId,$amount)
    {
        $token = $this->getToken();
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::TOPUP_URL_CONFIG_PATH);

        $data = array(
            "distributionId"    => $distributionId,
            "amount"    => $amount
        );
        $data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$token
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        return $response;
        /*
         * 200
        Content-Type: application/json
        {
          "cardNo": "12312312312312",
          "balance": "100000",
          "expired": "01/01/2017",
          "status": "active",
          "merchant": "Starbucks Coffee",
          "approvalCode": "123456",
          "trxNo": "1234567890",
          "trxType": "topup",
          "trxAmount": "250000",
          "trxCode": "1234567890123456123213123",
          "trxStatus": "accepted",
          "trxTime": "2015-03-06T04:39:33.000Z",
          "terminalId": "1",
          "id": "1"
        }

        400
        Content-Type: application/json
        "err": "Error message"

        403
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function balance()
    {
        $token = $this->getToken();
        $distributionId = 'distributionId';
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::BALANCE_URL_CONFIG_PATH);
        $data = array(
            'distributionId'    => $distributionId,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        {
          "cardNo": "1234123412341231",
          "balance": "100000",
          "expired": "31/01/2016",
          "status": "active"
        }

        400
        Content-Type: application/json
        "err": "Error message"

        401
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function redeemFull()
    {
        $token = $this->getToken();
        $activationCode = 'activationCode';
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::REDEEM_FULL_URL_CONFIG_PATH);
        $data = array(
            'activationCode'    => $activationCode,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        {
          "cardNo": "12312312312312",
          "balance": "100000",
          "approvalCode": "123456",
          "trxCode": "1234567890123456123213123",
          "trxStatus": "accepted",
          "trxTime": "2015-03-06T04:39:33.000Z",
          "id": "1"
        }

        400
        Content-Type: application/json
        "err": "Error message"

        403
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function balanceHold($cardNumber, $amount)
    {
        $token = $this->getToken();
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::BALANCE_HOLD_URL_CONFIG_PATH);
        $data = array(
            'cardNumber'    => $cardNumber,
            'amount'        => $amount,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        {
          "transactionKey": "ABC123",
          "balance": 123456,
          "autoReverseTimeout": "2015-03-06T04:39:33.000Z"
        }

        400
        Content-Type: application/json
        {
            title: "error title",
            message: "error message",
            error: ErrorStacktrace
        }

        401
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function balanceResolve()
    {
        $token = $this->getToken();
        $transactionSignature = 'ABCDE1234567890';
        $resolution = 'resolution';
        $resolutionKey = 'resolutionKey';
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::BALANCE_RESOLVE_URL_CONFIG_PATH);
        $data = array(
            'transactionSignature'  => $transactionSignature,
            'resolution'            => $resolution,
            'resolutionKey'         => $resolutionKey,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        {
          "resolutionSignature": "ABCDE1234567890",
          "resolutionResult": "released|reversed",
          "resolutionTimestamp": "2015-03-06T04:39:33.000Z",
          "balance": 123456
        }

        400
        Content-Type: application/json
        {
            title: "error title",
            message: "error message",
            error: ErrorStacktrace
        }

        401
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function transactionsList($periodStart, $periodEnd)
    {
        $token = $this->getToken();
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::TRANSACTIONS_LIST_URL_CONFIG_PATH);
        $data = array(
            'periodStart'   => $periodStart,
            'periodEnd'     => $periodEnd,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        [
          {
            "cardNo": "15312312312312",
            "merchant": "Starbucks Coffee",
            "trxType": "activation",
            "trxAmount": "500000",
            "trxDate": "2015-03-06",
            "trxTime": "08:39:33",
            "id": "3"
          },
          {
            "cardNo": "12316312312312",
            "merchant": "Starbucks Coffee",
            "trxType": "topup",
            "trxAmount": "400000",
            "trxDate": "2015-03-06",
            "trxTime": "04:39:33",
            "id": "2"
          },
          {
            "cardNo": "12312392312312",
            "merchant": "Starbucks Coffee",
            "trxType": "redeem",
            "trxAmount": "250000",
            "trxDate": "2015-03-05",
            "trxTime": "07:39:33",
            "id": "1"
          }
        ]

        400
        Content-Type: application/json
        "err": "Error message"

        401
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function transaction($transactionId)
    {
        $token = $this->getToken();
        $url = Mage::getStoreConfig(str_replace("{id}",$transactionId,Icube_GiftCard_Helper_Api_Data::TRANSACTION_URL_CONFIG_PATH));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        {
          "cardNo": "12312312312312",
          "balance": "100000",
          "expired": "01/01/2017",
          "status": "active",
          "merchant": "Starbucks Coffee",
          "approvalCode": "123456",
          "trxNo": "1234567890",
          "trxType": "activation",
          "trxAmount": "250000",
          "trxCode": "12345678901234561232131231234567890123456123213123",
          "trxStatus": "accepted",
          "trxTime": "2015-03-06T04:39:33.000Z",
          "distributor": {
            "id": 2,
            "company": "PT. Hypermart Indonesia",
            "brand": "Hypermart",
            "email": "info@hypermart.com",
            "phone": "83483434",
            "billingAddress": "Jl. Kencana Pualam No 91, Jakarta",
            "npwp": "64.3049.394.494"
          },
          "terminal": {
            "id": 7,
            "name": "hypermart1"
          },
          "store": {
            "id": 1,
            "location": "Allianz Tower",
            "phone": "83483434"
          }
        }

        400
        Content-Type: application/json
        "err": "Error message"

        401
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function transactionVoid($trxNo)
    {
        $token = $this->getToken();
        $url = Mage::getStoreConfig(Icube_GiftCard_Helper_Api_Data::TRANSACTION_VOID_URL_CONFIG_PATH);
        $data = array(
            'trxNo'     => $trxNo,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        {
          "cardNo": "12312312312312",
          "balance": "100000",
          "expired": "01/01/2017",
          "status": "active",
          "merchant": "Starbucks Coffee",
          "approvalCode": "123456",
          "trxNo": "1234567890",
          "trxType": "void",
          "trxAmount": "250000",
          "trxCode": "1234567890123456123213123",
          "trxStatus": "accepted",
          "trxTime": "2015-03-06T04:39:33.000Z",
          "trxNoVoid": "1234567890",
          "terminalId": "1",
          "id": "1"
        }

        400
        Content-Type: application/json
        "err": "Error message"

        401
        Content-Type: application/json
        "Unauthorized"
         * */
    }

    public function transactionReceipt($transactionId, $email = array())
    {
        $token = $this->getToken();
        $url = Mage::getStoreConfig(str_replace("{id}",$transactionId,Icube_GiftCard_Helper_Api_Data::TRANSACTION_RECEIPT_URL_CONFIG_PATH));
        $data = array(
            'email'     => $email,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            'Authorization: "Bearer '.$token.'"'
        ));

        $response = curl_exec($ch);
        curl_close($ch);

//        var_dump($response);

        $response = json_decode($response);
        /*
         * 200
        Content-Type: application/json
        {
          "email": [
            "recipient@email.address"
          ]
        }

        400
        Content-Type: application/json
        {
            title: "error title",
            message: "error message",
            error: ErrorStacktrace
        }

        401
        Content-Type: application/json
        "Unauthorized"
         * */
    }
}
