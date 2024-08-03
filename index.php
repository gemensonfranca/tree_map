<?php

    include_once "php/conexao.php";

    echo "<p style='margin-top: 0px; color: #ffffff;'>Deus é Fiel</p>";

    $dataset = [
        ['id' => 1, 'website' => 'Google.com', '2013' => 782800000, '2023' => 4589000000],
        ['id' => 2, 'website' => 'Youtube.com', '2013' => 2407000000, '2023' => 1407000000],
        ['id' => 3, 'website' => 'Facebook.com', '2013' => 836700000, '2023' => 840500000],
        ['id' => 4, 'website' => 'Globo.com', '2013' => 794800000, '2023' => 694800000],
        ['id' => 5, 'website' => 'Instagram.com', '2013' => 677700000, '2023' => 677700000],
        ['id' => 6, 'website' => 'Whatsapp.com', '2013' => 567100000, '2023' => 467100000],
    ];

    $dados_anteri = array($dataset[0]['2013'], $dataset[1]['2013'], $dataset[2]['2013'], $dataset[3]['2013'], $dataset[4]['2013'], $dataset[5]['2013']);
    $dados_atuais = array($dataset[0]['2023'], $dataset[1]['2023'], $dataset[2]['2023'], $dataset[3]['2023'], $dataset[4]['2023'], $dataset[5]['2023']);
    $quantGrupo   = count($dados_atuais);

    // CALCULOS DE CRESCIMENTO
    $groupsUpDown = array();

    for ($i=0; $i < $quantGrupo; $i++) { 

        $valor1 = $dados_anteri[$i];
        $valor2 = $dados_atuais[$i];

        if ($valor1 > $valor2) {
            $maior_valor = $valor1;
            $menor_valor = $valor2;
            $valor_nivel = "down";
        }
        elseif($valor1 == $valor2){
            $maior_valor = $valor2;
            $menor_valor = $valor1;
            $valor_nivel = "up";
        }
        else{
            $maior_valor = $valor2;
            $menor_valor = $valor1;
            $valor_nivel = "up";
        }

        $porcent1 = $menor_valor * 100 / $maior_valor;
        $cresdimn = 100 - $porcent1;

        $resultado = [$cresdimn, $valor_nivel];

        array_push($groupsUpDown, $resultado);
    }

    // ESCALA DE CORES
    $escala = [
        'up'   => ['99c58f', '77b26b', '529e48', '228b22'],
        'down' => ['ff9e81', 'ff7b5a', 'ff5232', 'ff0000'],
    ];

    $define = array();
    foreach ($groupsUpDown as $def) {
        
        if ($def[0] > 0 AND $def[0] < 26) {
            $color = $escala[$def[1]][0];
        }
        if ($def[0] > 24 AND $def[0] < 51) {
            $color = $escala[$def[1]][1];
        }
        if ($def[0] > 49 AND $def[0] < 76) {
            $color = $escala[$def[1]][2];
        }
        if ($def[0] > 74 AND $def[0] < 100) {
            $color = $escala[$def[1]][3];
        }

        array_push($define, $color);
    }

    // QUAL É MEU MAIOR GRUPO
    $total_geral  = array_sum($dados_atuais);
    $maior_todos  = max($dados_atuais);

    $total_level1 = $dados_atuais[0] + $dados_atuais[1];
    $total_level2 = $dados_atuais[2] + $dados_atuais[3] + $dados_atuais[4] + $dados_atuais[5];
    $total_level3 = $dados_atuais[4] + $dados_atuais[5];

    $level1 = [
        $dados_atuais[0] * 100 / $total_level1,
        $dados_atuais[1] * 100 / $total_level1,
    ];

    $level2 = [
        $dados_atuais[2] * 100 / $total_level2, 
        $dados_atuais[3] * 100 / $total_level2, 
        $dados_atuais[4] * 100 / $total_level2, 
        $dados_atuais[5] * 100 / $total_level2, 
    ];

    $level3 = [
        $dados_atuais[4] * 100 / $total_level3, 
        $dados_atuais[5] * 100 / $total_level3,   
    ];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tree Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<style>
    html,body{
        margin: 0;
        padding: 0;
        background: #fff
    }
    #grid {
        width: 100%;
        height: 90vh; 
        float: left;
        border: solid #000 1px;
    }
    .borderv{
        opacity: 1;
        border: 1px solid #000;
        cursor: pointer;
        transition: 500ms;
    }
    .borderv:hover{
        opacity: 0.9;
        border: 1px solid #000;
        cursor: pointer;
        transition: 500ms;
    }
    p.text_tree{
        font-weight: bold;
        color: #ffffff;
        margin: 10px 0px 0px 10px;
    }

</style>

<body style="width: 90%; margin: 0px 5%;">
    <div class="row">
        <div class="col-6">
            <h2><b>Tree Map Itens</b></h2>
            <p>Inclua itens e valores para visualizar no Tree Map</p>
            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-sm">
                <thead>
                    <tr>
                    <th scope="col"></th>
                    <th scope="col">Website</th>
                    <th scope="col">2013</th>
                    <th scope="col">2023</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    $continua = 0;
                    foreach ($dataset as $line) {
                        $id      = $line['id'];
                        $site    = $line['website'];
                        $passado = $line['2013'];
                        $atual   = $line['2023'];

                        echo '
                            <tr">
                                <td>'.$id.'</td>
                                <td>
                                    <input style="background-color: #'.$define[$continua].'; color: #ffffff;" type="text" value="'.$site.'" class="form-control" id="exampleInputPassword1">
                                </td>
                                <td>
                                    <input type="number" value="'.$passado.'" class="form-control" id="exampleInputPassword1">
                                </td>
                                <td>
                                    <input type="number" value="'.$atual.'" class="form-control" id="exampleInputPassword1">
                                </td>
                            </tr>
                        ';

                        $continua++;
                    }
                    
                    ?>
                </tbody>
                </table>
            </div>
            <small class="">Desenvolvido por Gemenson França &copy; <?php echo date('Y') ?></small>
            <button style="float: right; margin-left: 10px;" type="button" class="btn btn-primary btn-lg">Atualizar</button>
            <button style="float: right;" type="button" class="btn btn-secondary btn-lg">Resetar</button>
        </div>
        <div class="col-6">
            <div id="grid" style="display: show;">
                <!-- LEVEL1 -->
                <div style="width: 100%; display: inline-block;">   
                    <div class="borderv" style="width: <?php echo $level1[0] ?>%; height: <?php echo $level1[0] ?>%; background-color: <?php echo $define[0]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[0]['website']; ?></p></div>
                    <div class="borderv" style="width: <?php echo $level1[1] ?>%; height: <?php echo $level1[0] ?>%; background-color: <?php echo $define[1]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[1]['website']; ?></p></div>
                </div>
                <!-- LEVEL2 -->
                <div style="width: 100%; display: inline-block;">
                    <div class="borderv" style="width: <?php echo $level2[0] ?>%; height: <?php echo $level1[1] ?>%; background-color: <?php echo $define[2]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[2]['website']; ?></p></div>
                    <div class="borderv" style="width: <?php echo $level2[1] ?>%; height: <?php echo $level1[1] ?>%; background-color: <?php echo $define[3]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[3]['website']; ?></p></div>
                    <!-- LEVEL3 -->
                    <div style="width: <?php echo $level2[2] + $level2[3] ?>%; height: <?php echo $level1[1] ?>%; display: inline-block;">
                        <div class="borderv" style="width: 100%; height: <?php echo $level3[0] ?>%; background-color: <?php echo $define[4]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[4]['website']; ?></p></div>
                        <div class="borderv" style="width: 100%; height: <?php echo $level3[1] ?>%; background-color: <?php echo $define[5]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[5]['website']; ?></p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>