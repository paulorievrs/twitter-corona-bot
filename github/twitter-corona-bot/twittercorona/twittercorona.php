<?php
echo "Programa iniciado, buscando data...";
begin:
    $data = "";
    while($data != date("d/m/Y")) {
        sleep(600);
        $url = "https://pomber.github.io/covid19/timeseries.json";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resultado = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($resultado);


        foreach($json->Brazil as $days => $statistics) {
            $deaths = $statistics->deaths;
            $data = $statistics->date;
            $confirmed = $statistics->confirmed;
        }

        $data = data($data);
    }

    





echo "\nComeçando postagem...";

    $frase = "🔥ATUALIZAÇÕES DO CORONA VIRUS NO BRASIL🔥%0A%0A📅 " . $data . "%0A%0A✅ Casos confirmados: " . $confirmed . "%0A💀 Mortes Confirmadas: " . $deaths;
    $spc = " ";
    $espaco = "%20";
    $barraN="%0A";
    $frase = str_replace($spc, $espaco, $frase);

    echo "\nTentando postar: " . $frase;
    
        //Curl para enviar dados para o twitter, caracterizados para minha conta. Alterei dados para não publicar minha conta pessoal.

    $sh = curl_init();

    curl_setopt($sh, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/update.json');
    curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($sh, CURLOPT_POST, 1);
    curl_setopt($sh, CURLOPT_POSTFIELDS, "linkdotweet$frase");
    curl_setopt($sh, CURLOPT_ENCODING, 'gzip, deflate');

    $headers = array();
    $headers[] = 'User-Agent:0';
    $headers[] = 'Accept: */*';
    $headers[] = 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $headers[] = 'X-Twitter-Auth-Type: OAuth2Session';
    $headers[] = 'X-Twitter-Client-Language: pt';
    $headers[] = 'X-Twitter-Active-User: yes';
    $headers[] = 'X-Csrf-Token: ';
    $headers[] = 'Origin: https://twitter.com';
    $headers[] = 'Authorization: Bearer ';
    $headers[] = 'Referer: https://twitter.com/compose/tweet';
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Cookie: ';
    $headers[] = 'Te: Trailers';
    curl_setopt($sh, CURLOPT_HTTPHEADER, $headers);


$result = json_decode(curl_exec($sh));
echo "\n\n";


if(isset($result->created_at)) {
    echo "\n\nPostado com sucesso\n\n";
    $hora = date("H:i");
    $partido = explode(":", $hora);
    
    $faltaH = 24 - $partido[0];
    $faltaM = 59 - $partido[1]; 
    $faltaMs = $faltaM * 60;
    $faltaHs = $faltaH * 3600;
    sleep($faltaMs + $faltaHs);
    goto begin;
    
} else {
    foreach($result as $i) {
        echo $i[0]->message;
        break;
    }
    
    echo "\n\nPostagem não concluida\n\n";
    $hora = date("H:i");
    $partido = explode(":", $hora);
    $faltaH = 24 - $partido[0];
    $faltaM = 59 - $partido[1]; 
    $faltaMs = $faltaM * 60;
    $faltaHs = $faltaH * 3600;
    sleep($faltaMs + $faltaHs);
    goto begin;
}


if (curl_errno($sh)) {
    echo 'Error:' . curl_error($sh);
}
curl_close($sh);


function data($data){
    return date("d/m/Y", strtotime($data));
}

    
?>