<?php
/*  должен прийти массив 
$count_data['date'][$i] = '';
$count_data['count_1'][$i] = '';
$count_data['count_2'][$i] = '';
$count_data['count_3'][$i] = '';
 */

if (is_dir(__DIR__.'/graph')) {
    require_once ('graph/jpgraph.php');
    require_once ('graph/jpgraph_line.php');
    
    // Setup the graph
    $graph = new Graph(500,400);
    $graph->SetScale("textlin");
    
    $theme_class=new UniversalTheme;
    
    $graph->SetTheme($theme_class);
    $graph->img->SetAntiAliasing(false);
    $graph->SetBox(false);
    
    $graph->SetMargin(50,15,0,60);// отсупы у картинки -  слева, справа, сверху, снизу
    $graph->legend->SetPos(0.5,0.98,'center','bottom');// размещение легенты - подпсей

    $graph->img->SetAntiAliasing();
    
    $graph->yaxis->HideZeroLabel();
    $graph->yaxis->HideLine(false);
    $graph->yaxis->HideTicks(false,false);
    
    $graph->xgrid->Show();
    $graph->xgrid->SetLineStyle("solid");
    $count_data['date'] = array_reverse($count_data['date']);//разворот массива
    $graph->xaxis->SetTickLabels($count_data['date']);
    $graph->xgrid->SetColor('#E3E3E3');
    
    // 1 линия
    $count_data['count_1'] = array_reverse($count_data['count_1']);//разворот массива
    $p1 = new LinePlot($count_data['count_1']);
    $graph->Add($p1);
    $p1->SetColor("#19D300");
    $p1->SetLegend('Зашли в бот');    
    //$graph->legend->SetFrameWeight(1);

    // 2 линия
    if ($count_data['count_2']) {
        $count_data['count_2'] = array_reverse($count_data['count_2']);//разворот массива
        $p2 = new LinePlot($count_data['count_2']);
        $graph->Add($p2);
        $p2->SetColor("#00A6FF");
        $p2->SetLegend('Подписались на старте');    
        //$graph->legend->SetFrameWeight(1);
    }
    if ($count_data['count_3']) {
        $count_data['count_3'] = array_reverse($count_data['count_3']);//разворот массива
        $p3 = new LinePlot($count_data['count_3']);
        $graph->Add($p3);
        $p3->SetColor("#8E00FF");
        $p3->SetLegend('До сих пор на канале');    
        //$graph->legend->SetFrameWeight(1);
    }
    
    $graph->title->Set("Статистика за последние 30 дней");
    
    
    
    //$graph->Stroke(); 
     
    
    //сохранит картинку
    $gdImgHandler = $graph->Stroke(_IMG_HANDLER);
    $strtotime = strtotime('now'); 
    $graph_image = "graph/graph_".$strtotime.".jpg";
    $graph->img->Stream($graph_image);
    
    //показать на экране
    //$graph->Stroke();    
    
    //echo '<img src="'.$website.'/'.$JpGraph_image.'">';

}

?>