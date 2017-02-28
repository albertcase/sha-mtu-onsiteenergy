<?php

namespace Wechat\ApiBundle\Modals\classes;

class MyPHPExcel{

  public function output($data, $top , $name){
    $PHPExcel = new \PHPExcel();
    $PHPExcel->getProperties()->setTitle("Office 2007 XLSX Document")
							 ->setSubject("Office 2007 XLSX Document")
							 ->setDescription("XLSX document")
							 ->setKeywords("office 2007");
    $title = array();
    $keys = array_keys($data[0]);
    $k = 65;
    foreach($keys as $x){
      $title[$x] = chr($k);
      $k++;
    }
    foreach($top as $x => $x_val){
      $PHPExcel->setActiveSheetIndex(0)->setCellValue($title[$x].'1', $x_val);
      $PHPExcel->getActiveSheet()->getStyle($title[$x].'1')->getFont()->setSize(11);
      $PHPExcel->getActiveSheet()->getStyle($title[$x].'1')->getFont()->setBold(true);
      $PHPExcel->getActiveSheet()->getStyle($title[$x].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $PHPExcel->getActiveSheet()->getStyle($title[$x].'1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    $count = count($data);
    for($x = 0; $x<$count; $x++){
      $z = $x+2;
      foreach($data[$x] as $d => $d_val){
        $PHPExcel->setActiveSheetIndex(0)->setCellValue($title[$d].$z, $d_val);
        $PHPExcel->getActiveSheet()->getStyle($title[$d].$z)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $PHPExcel->getActiveSheet()->getStyle($title[$d].$z)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
      }
    }
    $PHPExcel->setActiveSheetIndex(0);
    foreach($title as $x){
      $PHPExcel->getActiveSheet()->getColumnDimension($x)->setAutoSize(true);
    }
    $PHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight('19');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$name.'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
    $objWriter->save('php://output');
  }

  public function input($file){
    $PHPExcel = \PHPExcel_IOFactory::load($file);
    $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
    $highestRow = $sheet->getHighestRow(); // 取得总行数
    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
    return array(
      'sheet' => $sheet,
      'highestRow' => $highestRow,
      'highestColumm' => $highestColumm,
    );
  }

}
