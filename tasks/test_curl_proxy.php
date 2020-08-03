<?php
ini_set('display_errors', 1);
//обновление счета
if( $curl = curl_init() && 1==2) {

    curl_setopt($curl, CURLOPT_URL, "https://bartercoin.holding.bz/bankbalance"); //'https://edge.qiwi.com/funding-sources/v1/accounts/current'
    
//    curl_setopt($curl, CURLOPT_PROXY, "http://192.168.37.25"); 
    
    curl_setopt($curl, CURLOPT_PROXY, "5.45.64.97:3128"); // "192.168.37.25:3128"
    
    curl_setopt($curl, CURLOPT_HEADER, 0);
    
    //CURLOPT_HTTPPROXYTUNNEL, true
    
    //curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME); //CURLPROXY_SOCKS5_HOSTNAME CURLPROXY_SOCKS4

//    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//        'Accept: application/json',
//        'Content-Type: application/json',
//        'Authorization: Bearer ' . $token)//$qiwi_token
//    ); 
    
    //curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_PROXYPORT, '3128');
    //curl_setopt($curl, CURLOPT_PROXYUSERPWD, "hrm:sfgsg$%6546ergyertg");
    
    //curl_setopt($curl, CURLOPT_HEADER, 1); 
//    curl_setopt($curl, CURLOPT_NOBODY, 1); 
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 

    $curl_error = curl_error($curl);
    $out_count = curl_exec($curl);

}

//curl_close($curl);

//$myecho = json_encode($out_count);
//`echo " out_count: "  $myecho >>/home/bartercoin/tmp/qaz`;

//echo("content: " . $out_count);

//echo("error: " . $curl_error);

//$url = 'https://bartercoin.holding.bz/bankbalance';//'http://www.wikia.com/fandom';
//$proxy = '192.168.37.25:3128';//'5.45.64.97:3128'; '192.168.37.25:3128'
//$useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
//$proxyauth = 'user:password';

//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL,$url);

//curl_setopt($ch, CURLOPT_PROXY, $proxy);

//curl_setopt($ch, CURLOPT_PROXYTYPE,  CURLPROXY_SOCKS5); //CURLPROXY_SOCKS5_HOSTNAME CURLPROXY_SOCKS4  CURLPROXY_SOCKS5 CURLPROXY_SOCKS4A
//curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, "hrm:sfgsg$%6546ergyertg");
//curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
//curl_setopt($ch, CURLOPT_VERBOSE, TRUE);


//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch, CURLOPT_HEADER, 1);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
//curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//$curl_error = curl_error($ch);
//$curl_scraped_page = curl_exec($ch);
//curl_close($ch);

//$myecho = json_encode($curl_scraped_page);
//`echo " out_count: "  $myecho >>/home/bartercoin/tmp/qaz`;

//echo $curl_scraped_page;
//echo $curl_error;

//die();

//$proxy = "192.168.37.25";
//$proxyport = '3128';
//$useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
//$url = "https://bartercoin.holding.bz/bankbalance";//"https://bartercoin.holding.bz/bankbalance";
//$credentials = "hrm:sfgsg$%6546ergyertg";

//$ch = curl_init();
////curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,15);
////curl_setopt($ch, CURLOPT_HTTP_VERSION,'CURL_HTTP_VERSION_1_1' );
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_PROXYPORT, $proxyport);

//curl_setopt($ch, CURLOPT_PROXYAUTH,  CURLAUTH_BASIC);
//curl_setopt($ch, CURLOPT_PROXYUSERPWD,$credentials);
//curl_setopt($ch, CURLOPT_PROXYTYPE,  7); 
//curl_setopt($ch, CURLOPT_USERAGENT,$useragent);
//curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
//curl_setopt($ch, CURLOPT_TIMEOUT, 20);
//$result=curl_exec ($ch);
//$curl_error = curl_error($ch);
//curl_close ($ch);

//$myecho = json_encode($result);
//`echo " out_count: "  $myecho >>/home/bartercoin/tmp/qaz`;

//echo $result;
//echo $curl_error;


	$loginpassw = "hrm:asdsad23432423edfdsfsdfgrsgtYYYYYY";
    $proxy_ip = '192.168.37.25';
    $proxy_port = '1080';
    $url = 'https://edge.qiwi.com/funding-sources/v1/accounts/current';//"https://bartercoin.holding.bz/bankbalance";
    $token = '72baa209ca57d9a620f7de726eff96d5';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token)//$qiwi_token
                );
    curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $loginpassw);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //'Proxy-Authenticate: Basic')
    //);  
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
   
    $result = curl_exec($ch);
    $result = json_decode($result);
    $result = $result->accounts[0]->balance->amount;
    $curl_error = curl_error($ch);
    $curl_header = curl_getinfo($ch);

    curl_close($ch);
    
    $myecho = json_encode($result);
	`echo " out_count: "  $myecho >>/home/bartercoin/tmp/qaz`;
	
	echo ('result: ' . $result . '<br>');
	echo ('err: ' . $curl_error . '<br>');
	echo '<pre>';
	print_r($curl_header);
	echo '</pre>';
	
	//function HandleHeaderLine( $ch, $header_line ) {
	    //echo "<br>YEAH: ".$header_line; // or do whatever
	    //return strlen($header_line);
	//}


//$out_count = json_decode($out_count);

//$out_count = $out_count->accounts[0]->balance->amount;
//            file_put_contents('bankbalance'.$b, $out_count);

//Иван Лобаноы, [27 окт. 2018 г., 13:30:48]:
//192.168.37.25
//3128 proxy
//1080 socks
//юзер hrm
//пароль sfgsg$%6546ergyertg
