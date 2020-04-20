<?php

begin:
    $title = "";
    $achou = false;
    echo "\nComeçou a buscar titulos correspondentes...\n";
    while($achou == false) { //enquanto não achar títulos que correspondem a minha busca

            sleep(5);
            $url = "http://newsapi.org/v2/top-headlines?sources=google-news-br&apiKey=46c6dc7ea4a24a7bb695a2dcf33fe562"; //API de notícias

            $sh = curl_init();
            
            curl_setopt($sh, CURLOPT_URL, $url);
            
            curl_setopt($sh, CURLOPT_RETURNTRANSFER, true);
            
            $resultado = curl_exec($sh);
            
            curl_close($sh);
            
            $json = json_decode($resultado);
            


            foreach($json->articles as $i) {
                
                $test = $i->title;
                echo "\nApi retornou: " . $test . "\n";  

                if(filtrar($test)) {
                    $title = $i->title;
                    $achou = true;
                    $url = $i->url;
                    echo "\nAchou: " . $title . "\n";
                    break;
                    
                }
            }
        }
        
    
        
        include_once '../bd/getTitles.php';
        


        if(!temEsseTitulo($title, $arrayTitle)) {

            echo "\n\nIncluindo titulo no banco: $title na hora: " . date("H:i") . "\n";

            include_once '../bd/insertNews.php';  

        } else {

            echo "\n\n O título $title já existe, erro ao inserir na hora: " . date("H:i") . " tentando novamente em 1h.\n";
            sleep(3600);
            goto begin;

        }

        if((strlen($title) + strlen($url)) > 280) {
            sleep(3600);
            goto begin;
        }




        $frase = $title . "%0A%0A" . $url;
        $spc = " ";
        $espaco = "%20";
        $frase = str_replace($spc, $espaco, $frase); //alterando os espaçamentos para mandar para url
        
        echo $frase . "\n";

        //Curl para enviar dados para o twitter, caracterizados para minha conta. Alterei dados para não publicar minha conta pessoal.
        $sh = curl_init();

        curl_setopt($sh, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/update.json');
        curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sh, CURLOPT_POST, 1);
        curl_setopt($sh, CURLOPT_POSTFIELDS, "url-twitter" . $frase);
        curl_setopt($sh, CURLOPT_ENCODING, 'gzip, deflate');
        
        $headers = array();
        $headers[] = 'User-Agent: ';
        $headers[] = 'Accept: */*';
        $headers[] = 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'X-Twitter-Auth-Type: OAuth2Session';
        $headers[] = 'X-Twitter-Client-Language: pt';
        $headers[] = 'X-Twitter-Active-User: yes';
        $headers[] = 'X-Csrf-Token: 9';
        $headers[] = 'Origin: https://twitter.com';
        $headers[] = 'Authorization: Bearer ';
        $headers[] = 'Referer: https://twitter.com/compose/tweet';
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Cookie: ';
        $headers[] = 'Te: Trailers';
        curl_setopt($sh, CURLOPT_HTTPHEADER, $headers);
        
        $result = json_decode(curl_exec($sh));


            
        if(isset($result->created_at)) { //quando é postado retorna quando foi criado, se não foi há um erro de retorno.
            echo "\n\nPostado com sucesso\n";
            echo "\n\n" . date("H:i") . " procurando outro post em 1 hora.\n";
            sleep(3600);
            goto begin;
            
        } else {
            echo "\n\nNão foi postado\n";
            var_dump($result);
            echo "\n\n" . date("H:i") . " tentando novamente em 1 hora.\n";
            sleep(3600);
            goto begin;
            
        }
            
        


        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        

//------Funções------------//
        function filtrar($name) {
            $retorno = false;
            $name = strtolower($name);
            $exploded = explode(" ", $name);
            for($i = 0; $i < sizeof($exploded); $i++) {
        
                if($exploded[$i] == "corona" || $exploded[$i] == "covid" || $exploded[$i] == "covid-19" || $exploded[$i] == "coronavírus" || $exploded[$i] == "saúde" || $exploded[$i] == "coronavírus:" || $exploded[$i] == "covid-19:") {
                    
                    $retorno = true;
                    break;
                }
        
            }
            return $retorno;
        
        }
        
        function temEsseTitulo($title, $arrayTitle) {
            $aspas = "'";
            $aspasDuplas = '"';
            $title = str_replace($aspas, $aspasDuplas, $title);
            for($i = 0; $i < count($arrayTitle); $i++) {
                if($title == $arrayTitle[$i]) {
                    return true;
                }
            }
        
            return false;
        }
        
?>