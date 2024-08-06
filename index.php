<?php

    session_start();

    include_once "php/conexao.php";

    echo "<p style='margin-top: 0px; color: #ffffff;'>Deus é Fiel</p>";

    $campos = [
        @$_GET['fonte_a_1'],
        @$_GET['fonte_b_1'],
        @$_GET['fonte_a_2'],
        @$_GET['fonte_b_2'],
        @$_GET['fonte_a_3'],
        @$_GET['fonte_b_3'],
        @$_GET['fonte_a_4'],
        @$_GET['fonte_b_4'],
        @$_GET['fonte_a_5'],
        @$_GET['fonte_b_5'],
        @$_GET['fonte_a_6'],
        @$_GET['fonte_b_6'],
    ];

    foreach ($campos as $campo) {
        if ($campo === "") {
            $_SESSION['flash_error'] = "Todos os campos são obrigatórios!";
        }
    }

    $stmt = $conn->prepare('SELECT * FROM itens_default ORDER BY fonte2 DESC');
    $stmt->execute();
    $results = $stmt->fetchAll();

    $dataset = array();
    foreach ($results as $key) {
        $id     = $key['id'];
        $nome   = $key['nome'];
        $fonte1 = $key['fonte1'];
        $fonte2 = $key['fonte2'];

        $array  = array('id' => $id, 'nome' => $nome, 'fonte1' => intval($fonte1), 'fonte2' => intval($fonte2));
        array_push($dataset, $array);
    }

    if (isset($_GET['atualizar'])) {
        $dados_anteri = array($campos[0], $campos[2], $campos[4], $campos[6], $campos[8], $campos[10]);
        $dados_atuais = array($campos[1], $campos[3], $campos[5], $campos[7], $campos[9], $campos[11]);
    }
    else{
        $dados_anteri = array($dataset[0]['fonte1'], $dataset[1]['fonte1'], $dataset[2]['fonte1'], $dataset[3]['fonte1'], $dataset[4]['fonte1'], $dataset[5]['fonte1']);
        $dados_atuais = array($dataset[0]['fonte2'], $dataset[1]['fonte2'], $dataset[2]['fonte2'], $dataset[3]['fonte2'], $dataset[4]['fonte2'], $dataset[5]['fonte2']);
    }

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

            <div style="width: 100%; height: 50px; display: inline-block">
                <div style="width: 12.5%; float: left; height: 20px; background-color: #ff0000;"></div>
                <div style="width: 12.5%; float: left; height: 20px; background-color: #ff5232;"></div>
                <div style="width: 12.5%; float: left; height: 20px; background-color: #ff7b5a;"></div>
                <div style="width: 12.5%; float: left; height: 20px; background-color: #ff9e81;"></div>
                <div style="width: 12.5%; float: left; height: 20px; background-color: #99c58f;"></div>
                <div style="width: 12.5%; float: left; height: 20px; background-color: #77b26b;"></div>
                <div style="width: 12.5%; float: left; height: 20px; background-color: #529e48;"></div>
                <div style="width: 12.5%; float: left; height: 20px; background-color: #228b22;"></div>
            </div>
            <div style="width: 100%; display: inline-block; margin-top: -20px;">
                <small style="float: left;">Maior Queda</small>
                <small style="float: right;">Maior Crescimento</small>
            </div>

            <?php
            
            if (isset($_SESSION['flash_error'])) {

                echo "
                    <div class='alert alert-danger' role='alert'>".$_SESSION['flash_error']."</div>
                ";
    
                unset($_SESSION['flash_error']);
            }
            
            ?>
            
            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-sm">
                <thead>
                    <tr>
                    <th scope="col"></th>
                    <th scope="col">Nome</th>
                    <th scope="col">Fonte 1</th>
                    <th scope="col">Fonte 2</th>
                    </tr>
                </thead>
                <tbody>
                    <form action="" method="get">
                    <?php
                    
                    $continua = 0;
                    $position = 1;
                    foreach ($dataset as $line) {
                        $id      = $line['id'];
                        $nomex   = $line['nome'];
                        $passado = $line['fonte1'];
                        $atual   = $line['fonte2'];

                        echo '
                            <tr">
                                <td>'.$position.'</td>
                                <td>
                                    <input style="background-color: #'.$define[$continua].'; color: #ffffff;" type="text" value="'.$nomex.'" class="form-control" id="exampleInputPassword1">
                                </td>';

                                if (isset($_GET['atualizar'])) {
                                    echo '
                                    
                                    <td>
                                        <input type="number" name="fonte_a_'.$id.'" value="'.$dados_anteri[$continua].'" class="form-control" id="exampleInputPassword1">
                                    </td>
                                    <td>
                                        <input type="number" name="fonte_b_'.$id.'" value="'.$dados_atuais[$continua].'" class="form-control" id="exampleInputPassword1">
                                    </td>
                                    
                                    ';
                                }
                                else{
                                    echo '
                                    
                                    <td>
                                        <input type="number" name="fonte_a_'.$id.'" value="'.$dataset[$continua]['fonte1'].'" class="form-control" id="exampleInputPassword1">
                                    </td>
                                    <td>
                                        <input type="number" name="fonte_b_'.$id.'" value="'.$dataset[$continua]['fonte2'].'" class="form-control" id="exampleInputPassword1">
                                    </td>
                                    
                                    ';
                                }

                                echo '
                            </tr>

                            <div class="modal fade" id="exampleModal'.$id.'" tabindex="-1" aria-labelledby="exampleModalLabel'.$id.'" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>'.$nomex.'</h5>';

                                            if ($define[$continua] == $escala['up'][0] OR $define[$continua] == $escala['up'][1] OR $define[$continua] == $escala['up'][2] OR $define[$continua] == $escala['up'][3]) {
                                                echo '<span style="color: green;">▲ '.round($groupsUpDown[$continua][0], 2).'%</span>';
                                            }
                                            else{
                                                echo '<span style="color: red;">▼ '.round($groupsUpDown[$continua][0], 2).'%</span>';
                                            }

                                            echo '
                                            <span> - Fonte 1: '.$passado.'</span>
                                            <span> - Fonte 2: '.$atual.'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ';

                        $continua++;
                        $position++;
                    }
                    
                    ?>
                    
                </tbody>
                </table>
            </div>
            <small class="">Desenvolvido por Gemenson França &copy; <?php echo date('Y') ?></small>
            <button style="float: right; margin-left: 10px;" type="submit" name="atualizar" class="btn btn-primary btn-lg">Atualizar</button>
            </form>
            <a href="index.php"><button style="float: right; margin-left: 10px;" type="button" class="btn btn-secondary btn-lg">Resetar</button></a>
        </div>
        <div class="col-6">
            <div id="grid" style="display: show;">
                <!-- LEVEL1 -->
                <div style="width: 100%; display: inline-block;">   
                    <div data-bs-toggle="modal" data-bs-target="#exampleModal1" class="borderv" style="width: <?php echo $level1[0] ?>%; height: <?php echo $level1[0] ?>%; background-color: <?php echo $define[0]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[0]['nome']; ?></p></div>
                    <div data-bs-toggle="modal" data-bs-target="#exampleModal2" class="borderv" style="width: <?php echo $level1[1] ?>%; height: <?php echo $level1[0] ?>%; background-color: <?php echo $define[1]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[1]['nome']; ?></p></div>
                </div>
                <!-- LEVEL2 -->
                <div style="width: 100%; display: inline-block;">
                    <div data-bs-toggle="modal" data-bs-target="#exampleModal3" class="borderv" style="width: <?php echo $level2[0] ?>%; height: <?php echo $level1[1] ?>%; background-color: <?php echo $define[2]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[2]['nome']; ?></p></div>
                    <div data-bs-toggle="modal" data-bs-target="#exampleModal4" class="borderv" style="width: <?php echo $level2[1] ?>%; height: <?php echo $level1[1] ?>%; background-color: <?php echo $define[3]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[3]['nome']; ?></p></div>
                    <!-- LEVEL3 -->
                    <div style="width: <?php echo $level2[2] + $level2[3] ?>%; height: <?php echo $level1[1] ?>%; display: inline-block;">
                        <div data-bs-toggle="modal" data-bs-target="#exampleModal5" class="borderv" style="width: 100%; height: <?php echo $level3[0] ?>%; background-color: <?php echo $define[4]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[4]['nome']; ?></p></div>
                        <div data-bs-toggle="modal" data-bs-target="#exampleModal6" class="borderv" style="width: 100%; height: <?php echo $level3[1] ?>%; background-color: <?php echo $define[5]; ?>; float: left;"><p class="text_tree"><?php echo $dataset[5]['nome']; ?></p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>