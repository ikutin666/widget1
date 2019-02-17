<?php
header('Access-Control-Allow-Origin: *');
if(isset($_GET['id']) && isset($_GET['api_key']) && isset($_GET['login']) && isset($_GET['login']) && isset($_GET['account'])) {
    $subdomain=$_GET['account'];
    $id=$_GET['id'];
   $api_key=$_GET['api_key'];
   $login=$_GET['login'];
    require_once '/vendor/autoload.php';
    require_once '/vendor/twig/twig/lib/Twig/Autoloader.php';
    function api_crm_get($api, $name, $subdomain)
    {
        $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
        $link = 'https://' . $subdomain . '.amocrm.ru/api/v2/' . $api . '?' . $name;
       // echo $link;
        #Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable'
        );
        try {
            #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
            if ($code != 200 && $code != 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
            } else {

                return json_decode($out);
            }
        } catch (Exception $E) {
            die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
        }
    }

    function api_crm($api, $method, $param, $subdomain)
    {

        if ($api == 'auth') {
            $link = 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json';
            $lead = $param;
        } else {
            $lead[$method] = $param;
            $link = 'https://' . $subdomain . '.amocrm.ru/api/v2/' . $api;
            // echo $link;
        }

        #Формируем ссылку для запроса
        /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
        работе с этой
        библиотекой Вы можете прочитать в мануале. */
        $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
        #Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        // echo '<br>';
        // echo json_encode($lead);
        // echo '<br>';
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($lead));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable'
        );
        try {
            #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
            if ($code != 200 && $code != 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
            } else {

                return json_decode($out);
            }
        } catch (Exception $E) {
            die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
        }

    }

    Twig_Autoloader::register();
    $loader = new  \Twig\Loader\FilesystemLoader ('./template');
    $twig = new  \Twig\Environment ($loader);
    $subdomain = 'ikutin';
    $user = array(
        'USER_LOGIN' => $login,
        'USER_HASH' => $api_key
    );
#авторизируемся
 // echo var_dump(api_crm('auth', '', $user, $subdomain));


    $info_contacts= api_crm_get('contacts', 'id='.$id, $subdomain); //получаем информацию по id контакта
    $mas_leads_contact = $info_contacts->_embedded->items[0]->leads->id; //получем массив из id сделок

//$info_leads=api_crm_get('leads','id='.$mas_leads_contact[0],$subdomain);
//var_dump(,$info_contacts);
    $cards = [];
    foreach ($mas_leads_contact as $id_leads) {
        $info_leads = api_crm_get('leads', 'id=' . $id_leads, $subdomain);
        $name = $name = $info_leads->_embedded->items[0]->name;
        $cards[] = ['name' => $name, 'href' => '/leads/detail/' . $id_leads];
    }
//var_dump($cards);
    $template = $twig->load('blank2.html');
    echo $template->render(['cards' => $cards]);
}
?>
